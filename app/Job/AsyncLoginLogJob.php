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

namespace App\Job;

use App\Repository\SystemLogRepository;
use Hyperf\AsyncQueue\Job;

use function Hyperf\Support\make;

// 使用原始JOB方式
class AsyncLoginLogJob extends Job
{
    public $params;

    /**
     * 任务执行失败后的重试次数，即最大执行次数为 $maxAttempts+1 次
     */
    protected int $maxAttempts = 2;

    public function __construct($params)
    {
        // 这里最好是普通数据，不要使用携带 IO 的对象，比如 PDO 对象
        // 因为 Job 会被序列化，所以成员变量不要包含 匿名函数 等 无法被序列化 的内容，如果不清楚哪些内容无法被序列化，尽量使用注解方式。
        $this->params = $params;
    }

    public function handle()
    {
        /* @var SystemLogRepository $systemLogRepository */
        $systemLogRepository = make(SystemLogRepository::class);

        $systemLogRepository->createLog($this->params);
        return 'success';
    }
}
