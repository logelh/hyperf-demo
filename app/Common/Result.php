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

namespace App\Common;

class Result
{
    /**
     * 成功响应.
     * @param mixed $data 响应数据
     * @param string $message 提示信息
     * @param int $code 业务状态码
     */
    public static function success($data = null, string $message = '操作成功', int $code = 200): array
    {
        return [
            'code' => $code,
            'message' => $message,
            'data' => $data,
            'timestamp' => time(), // 自动生成当前时间戳
        ];
    }

    /**
     * 失败响应.
     * @param string $message 错误信息
     * @param int $code 业务错误码
     * @param mixed $data 附加数据（可选）
     */
    public static function error(string $message = '操作失败', int $code = 500, $data = null): array
    {
        return [
            'code' => $code,
            'message' => $message,
            'data' => $data,
            'timestamp' => time(),
        ];
    }
}
