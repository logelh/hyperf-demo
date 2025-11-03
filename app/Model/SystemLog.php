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
 * php bin/hyperf.php gen:model system_logs 生成.
 * @property int $id id
 * @property int $user_id 操作用户ID
 * @property string $action 操作类型
 * @property string $description 操作描述
 * @property string $ip_address IP地址
 * @property string $user_agent 用户代理
 * @property string $request_data 请求数据
 * @property string $response_data 响应数据
 * @property Carbon $created_at
 */
class SystemLog extends Model
{
    // 登录日志
    public const LOGIN_ACTION = 'login';

    // 关闭update_at
    public const UPDATED_AT = null;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'system_logs';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'user_id',
        'action',
        'description',
        'ip_address',
        'user_agent',
        'request_data',
        'response_data',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'user_id' => 'integer', 'created_at' => 'datetime'];
}
