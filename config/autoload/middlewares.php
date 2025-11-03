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
use App\Middleware\AuthMiddleware;
use App\Middleware\FormatResponseMiddleware;
use Hyperf\Session\Middleware\SessionMiddleware;
use Hyperf\Validation\Middleware\ValidationMiddleware;

/*
 * This file is part of Hyperf.
 *
 * @see     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
return [
    'http' => [
        FormatResponseMiddleware::class,
        AuthMiddleware::class,
        ValidationMiddleware::class,
        SessionMiddleware::class, // 必须添加此行
    ],
];
