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
return [
    'default' => [
        'transport' => [
            'dsn' => 'smtp://logelh@163.com:NCjGNKq3Qfwm33mk@smtp.163.com:587', // NCjGNKq3Qfwm33mk 授权码
        ],
        'from' => [
            'address' => 'logelh@163.com', // 发件人邮箱
            'name' => '系统通知',
        ],
    ],
];
