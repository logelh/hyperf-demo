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

namespace App\Model;

use Carbon\Carbon;
use Qbhy\HyperfAuth\AuthAbility;
use Qbhy\HyperfAuth\Authenticatable;

/**
 * 用户模型.
 *
 * @property int $id
 * @property string $name 用户名
 * @property string $email 邮箱
 * @property string $password 密码
 * @property null|string $avatar 头像URL
 * @property string $role 角色 (user, author, admin)
 * @property string $status 状态 (active, inactive, banned)
 * @property int $points 积分
 * @property null|Carbon $last_login_at 最后登录时间
 * @property null|string $last_login_ip 最后登录IP
 * @property null|Carbon $email_verified_at 邮箱验证时间
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class User extends Model implements Authenticatable
{
    use AuthAbility;

    // 角色常量
    public const ROLE_USER = 'user';

    public const ROLE_AUTHOR = 'author';

    public const ROLE_ADMIN = 'admin';

    // 状态常量
    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    public const STATUS_BANNED = 'banned';

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'users';

    /**
     * 可批量复制字段
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'role',
        'status',
        'points',
        'last_login_at',
        'last_login_ip',
        'email_verified_at',
    ];

    /**
     * 隐藏字段.
     */
    protected array $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'points' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
