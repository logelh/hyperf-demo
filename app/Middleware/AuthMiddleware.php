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

namespace App\Middleware;

use App\Exception\BusinessException;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Qbhy\HyperfAuth\AuthManager;
use Qbhy\HyperfAuth\Guard\JwtGuard;
use Throwable;

class AuthMiddleware implements MiddlewareInterface
{
    #[Inject]
    protected AuthManager $auth;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // 无需验证token的接口
        $unCheck = [
            '/api/v1/auth/login',
            '/api/v1/auth/register',
        ];
        $uri = $request->getUri()->getPath();
        if (! in_array($uri, $unCheck)) {
            $request = $this->checkToken($request);
        }

        // 继续处理请求
        return $handler->handle($request);
    }

    /**
     * @throws Throwable
     */
    public function checkToken(ServerRequestInterface $request): ServerRequestInterface
    {
        // 1. 获取 Authorization 请求头
        $authorization = $request->getHeaderLine('Authorization');
        // getHeaderLine 会返回头信息的字符串形式（若有多个值则用逗号拼接）

        // 2. 验证格式是否为 Bearer {token} ，也可以在check方法里，让框架自动验证
        if (empty($authorization) || ! str_starts_with($authorization, 'Bearer ')) {
            throw new BusinessException(400, 'Authorization 头格式错误，应为 Bearer {jwt_token}');
        }

        // 3. 提取 JWT Token（去除前缀 'Bearer '）
        $jwtToken = substr($authorization, 7);
        // 使用 AuthManager 验证 token 是否有效
        /** @var JwtGuard $jwt */
        $jwt = $this->auth->guard('jwt');
        if (! $jwt->check($jwtToken)) {
            throw new BusinessException(401, 'Unauthorized');
        }

        $user = $jwt->user();
        return $request->withAttribute('user', $user);
    }
}
