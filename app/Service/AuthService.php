<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Service;

use App\Constants\CacheKey;
use App\Exception\BusinessException;
use App\Job\AsyncLoginLogJob;
use App\Model\SystemLog;
use App\Repository\UserRepository;
use App\Util\CommonUtil;
use App\Util\IpUtil;
use Hyperf\AsyncQueue\Driver\DriverFactory;
use Hyperf\AsyncQueue\Driver\DriverInterface;
use Hyperf\Contract\ContainerInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Redis\Redis;
use Qbhy\HyperfAuth\AuthManager;
use Qbhy\HyperfAuth\Guard\JwtGuard;

class AuthService
{
    #[Inject]
    protected UserRepository $userRepository;

    #[Inject]
    protected AuthManager $auth;

    #[Inject]
    protected Redis $redis;

    protected DriverInterface $driver;

    #[Inject]
    private ContainerInterface $container;

    public function __construct(DriverFactory $driverFactory)
    {
        $this->driver = $driverFactory->get('default');
    }

    public function register(array $data)
    {
        // Hash the password with bcrypt
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        // Create user record
        $user = $this->userRepository->create($data);

        $message = [
            'user_id' => $user->id,
            'email' => $user->email,
            'subject' => '欢迎注册',
            'content' => '感谢您的注册，点击链接激活账号：...',
            'created_at' => date('Y-m-d H:i:s'),
        ];
        // 自动生成消息ID
        $this->redis->xAdd(CacheKey::EMAIL_STREAM_KEY, '*', $message);
        return ['status' => true];
    }

    public function logout()
    {
        $this->auth->logout();
    }

    /**
     * - 登录方式：邮箱+密码
     * - 记住登录：7天内自动登录
     * - 登录限制：连续5次失败后锁定30分钟
     * - JWT token过期时间：2小时
     * - 支持token刷新机制
     * - 记录登录日志.
     */
    public function login(string $email, string $password): ?string
    {
        $lockKey = "login:lock:{$email}";
        $failKey = "login:fail:{$email}";

        // 检查是否处于锁定状态
        if ($this->redis->exists($lockKey)) {
            throw new BusinessException(403, '账户已被锁定，请稍后再试');
        }

        $request = $this->container->get(RequestInterface::class);
        $loginLog = [
            'description' => '用户登录日志',
            'action' => SystemLog::LOGIN_ACTION,
            'ip_address' => IpUtil::getClientIp($request),
            'user_agent' => CommonUtil::getUserAgent($request),
            'request_data' => json_encode($request->all()),
            'user_id' => 0,
        ];
        try {
            $token = $this->loginIn($email, $password);
            // 登录成功，清除失败计数
            $this->redis->del($failKey);

            $loginLog['user_id'] = $this->auth->id();
            $loginLog['response_data'] = json_encode(['token' => $token]);

            // 异步任务执行日志写入
            $this->driver->push(new AsyncLoginLogJob($loginLog));

            return $token;
        } catch (BusinessException $businessException) {
            $loginLog['response_data'] = json_encode(['message' => $businessException->getMessage()]);
            $this->driver->push(new AsyncLoginLogJob($loginLog));

            $this->incrementFailCount($failKey, $lockKey);
            throw new BusinessException($businessException->getCode(), $businessException->getMessage());
        }
    }

    public function refreshToken(array $data)
    {
        /** @var JwtGuard $jwt */
        $jwt = $this->auth->guard('jwt');
        return $jwt->refresh($data['refresh_token']);
    }

    /**
     *  实现真实登录.
     * - 密码加密存储（bcrypt）
     * - 登录成功：返回JWT token，记录登录时间和IP
     * - 登录失败：明确提示邮箱或密码错误.
     */
    protected function loginIn(string $email, string $password): string
    {
        $user = $this->userRepository->existsByEmail($email);

        if (! $user) {
            throw new BusinessException(406, '用户邮箱错误!');
        }

        // Verify password
        if (! password_verify($password, $user->password)) {
            throw new BusinessException(406, '用户密码错误!');
        }

        /** @var JwtGuard $jwtGuard */
        $jwtGuard = $this->auth->guard('jwt');
        // Generate JWT token
        return $jwtGuard->login($user);
    }

    protected function incrementFailCount(string $failKey, string $lockKey)
    {
        $failCount = $this->redis->incr($failKey);
        if ($failCount == 1) {
            // 第一次失败时，设置失败计数过期时间30分钟
            $this->redis->expire($failKey, 30 * 60);
        } elseif ($failCount >= 5) {
            // 达到5次失败，设置账号锁定30分钟
            $this->redis->setex($lockKey, 30 * 60, 1);
            $this->redis->del($failKey);
        }
    }
}
