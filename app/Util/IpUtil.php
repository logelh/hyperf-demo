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

namespace App\Util;

use Hyperf\HttpServer\Contract\RequestInterface;

class IpUtil
{
    /**
     * 获取客户端IP（基础方法）.
     */
    public static function getClientIp(RequestInterface $request): string
    {
        $headers = $request->getHeader('x-forwarded-for');
        if (! empty($headers)) {
            return $headers[0];
        }
        // 从服务器参数中获取直接IP（REMOTE_ADDR）
        $serverParams = $request->getServerParams();

        return $serverParams['remote_addr'] ?? 'unknown';
    }
}
