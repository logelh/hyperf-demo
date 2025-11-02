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

use App\Exception\BusinessException;
use App\Repository\UserRepository;
use Hyperf\Di\Annotation\Inject;
use Qbhy\HyperfAuth\AuthManager;

class AuthService
{
    #[Inject]
    protected UserRepository $userRepository;

    #[Inject]
    protected AuthManager $auth;

    public function register(array $data)
    {
        // 先检查用户名和邮箱唯一性
        if ($this->userRepository->existsByEmail($data['email'])) {
            throw new BusinessException(400, '邮箱已被注册');
        }

        // Hash the password with bcrypt
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        // Create user record
        $user = $this->userRepository->create($data);
        $status = $this->auth->guard('session')->login($user);
        return ['status' => $status];
    }

    public function logout()
    {
        $this->auth->logout();
    }
}
