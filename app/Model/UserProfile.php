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

/**
 * @property int $id id
 * @property int $user_id 用户id
 * @property string $bio 个人简介
 * @property string $website 个人网站
 * @property string $location 所在地
 * @property string $company 公司
 * @property string $github GitHub用户名
 * @property string $twitter Twitter用户名
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class UserProfile extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'user_profiles';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'user_id',
        'bio',
        'website',
        'location',
        'company',
        'github',
        'twitter',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'user_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
