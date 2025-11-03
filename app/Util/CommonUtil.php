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

class CommonUtil
{
    // 通过依赖注入获取 RequestInterface 实例
    public static function getUserAgent(RequestInterface $request)
    {
        // 获取 User-Agent
        $userAgent = $request->getHeaderLine('User-Agent');

        return $userAgent ?: '未知 User-Agent';
    }
}
