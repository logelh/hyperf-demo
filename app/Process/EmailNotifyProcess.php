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

namespace App\Process;

use App\Constants\CacheKey;
use App\Service\EmailService;
use Exception;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Process\AbstractProcess;
use Hyperf\Process\Annotation\Process;
use Hyperf\Redis\Redis;
use Psr\Container\ContainerInterface;
use RedisException;

#[Process(name: 'async-email-queue')]
class EmailNotifyProcess extends AbstractProcess
{
    #[Inject]
    protected Redis $redis;

    #[Inject]
    protected EmailService $emailService;

    public function __construct(protected ContainerInterface $container, protected StdoutLoggerInterface $logger)
    {
        parent::__construct($this->container);
    }

    public function handle(): void
    {
        $consumerGroup = 'email_group'; // 消费组名称
        $consumerName = 'email_consumer_1'; // 消费者名称

        try {
            $this->redis->xGroup('CREATE', CacheKey::EMAIL_STREAM_KEY, $consumerGroup, '0', true);
        } catch (RedisException $e) {
            // 组已经存在时候忽略
            if (! str_contains($e->getMessage(), 'BUSYGROUP')) {
                $this->logger->error('初始化消费组失败：' . $e->getMessage());
            }
        }

        while (true) {
            try {
                $messages = $this->redis->xReadGroup(
                    $consumerGroup,
                    $consumerName,
                    [CacheKey::EMAIL_STREAM_KEY => '>'], // '>' 表示读取未分配的消息
                    100, // 最多读取100条
                    5000 // 阻塞时间(毫秒)
                );

                if (! empty($messages[CacheKey::EMAIL_STREAM_KEY])) {
                    foreach ($messages[CacheKey::EMAIL_STREAM_KEY] as $messageId => $message) {
                        $this->emailService->send(
                            $message['email'],
                            $message['subject'],
                            $message['content']
                        );

                        $this->logger->info('邮件发送成功！消费消息！' . $messageId);
                        $this->redis->xAck(CacheKey::EMAIL_STREAM_KEY, $consumerGroup, [$messageId]);
                    }
                }
            } catch (Exception $exception) {
                $this->logger->error('Stream 监听错误：' . $exception->getMessage());
                sleep(1);
            }
        }
    }
}
