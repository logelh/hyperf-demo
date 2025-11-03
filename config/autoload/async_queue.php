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
use Hyperf\AsyncQueue\Driver\RedisDriver;

/*
 * This file is part of Hyperf.
 *
 * @see     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
return [
    'default' => [
        'driver' => RedisDriver::class,
        'redis' => [
            'pool' => 'default', // redis 连接池
        ],
        'channel' => 'async-queue:default', // Redis 队列的频道名称（自定义，确保唯一）
        'timeout' => 2, // pop 消息的超时时间
        // 'retry_seconds' => 5, // 失败后重新尝试间隔
        'retry_seconds' => [1, 5, 10, 20], // 更改为阶梯式的重试
        'handle_timeout' => 10, // 消息处理超时时间
        'processes' => 1, // 消费进程数
        'concurrent' => [
            'limit' => 10, // 同时处理消息数
        ],
        'max_messages' => 0, // 进程重启所需最大处理的消息数 默认不重启
    ],
];
