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
use App\Exception\Handler\GlobalExceptionHandler;
use Hyperf\HttpServer\Exception\Handler\HttpExceptionHandler;
use Qbhy\HyperfAuth\AuthExceptionHandler;

/*
 * This file is part of Hyperf.
 *
 * @see     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
return [
    'handler' => [
        'http' => [
            GlobalExceptionHandler::class,
            //            \Hyperf\Validation\ValidationExceptionHandler::class,
            //            HttpExceptionHandler::class,
            //
            //          此步骤可选，开发者可以自行捕捉 捕获AuthException 和 JWTException异常
            //                                    AuthExceptionHandler::class,
        ],
    ],
];
