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
use App\Repository\UserRepository;
use Hyperf\Di\Annotation\Inject;
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

    public function login(string $email, string $password): ?string
    {
        $user = $this->userRepository->existsByEmail($email);

        if (! $user) {
            return null;
        }

        // Verify password
        if (! password_verify($password, $user->password)) {
            return null;
        }

        /** @var JwtGuard $jwtGuard */
        $jwtGuard = $this->auth->guard('jwt');
        // Generate JWT token
        return $jwtGuard->login($user);
    }
}
