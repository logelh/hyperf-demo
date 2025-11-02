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

namespace App\Repository;

use App\Model\User;
use Hyperf\Database\Model\Model;
use Hyperf\Di\Annotation\Inject;

class UserRepository
{
    #[Inject]
    private User $user;

    /**
     * 是否已经存在邮件.
     */
    public function existsByEmail(string $email)
    {
        return $this->user::where('email', $email)->first();
    }

    /**
     * 用户创建.
     */
    public function create(array $data): Model|User
    {
        return $this->user::create($data);
    }
}
