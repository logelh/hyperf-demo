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

namespace App\Controller\V1;

use App\Common\Result;
use App\Controller\AbstractController;
use App\Request\AuthLoginRequest;
use App\Request\AuthRegisterRequest;
use App\Service\AuthService;
use App\Util\IpUtil;
use Carbon\Carbon;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\PostMapping;
use Qbhy\HyperfAuth\AuthMiddleware;

/**
 * 使用php bin/hyperf.php describe:routes 扫描所有路由地址
 */
#[Controller(prefix: '/api/v1/auth')]
class AuthController extends AbstractController
{
    #[Inject]
    protected AuthService $authService;

    /**
     * note:使用的时候不能带/，否则他会认为前面controller 前缀无效。只用register.
     */
    #[PostMapping(path: 'register')]
    public function register(AuthRegisterRequest $request)
    {
        $data = $request->validated();
        $data['last_login_at'] = Carbon::now();
        $data['last_login_ip'] = IpUtil::getClientIp($request);
        $user = $this->authService->register($data);

        return Result::success([
            'user' => $user,
        ]);
    }

    #[PostMapping(path: 'login')]
    public function login(AuthLoginRequest $request)
    {
        $data = $request->validated();
        $token = $this->authService->login($data['email'], $data['password']);
        if (! $token) {
            return Result::error('用户登录失败', 406);
        }

        return Result::success(['token' => $token]);
    }

    #[GetMapping(path: '/logout')]
    #[Middleware(AuthMiddleware::class)]
    public function logout()
    {
        $this->authService->logout();
        return [];
    }
}
