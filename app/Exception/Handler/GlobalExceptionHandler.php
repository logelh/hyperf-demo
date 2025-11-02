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

namespace App\Exception\Handler;

use App\Common\Result;
use App\Exception\BusinessException; // 自定义业务异常（见步骤3）
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\ExceptionHandler\Formatter\FormatterInterface;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Validation\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GlobalExceptionHandler extends ExceptionHandler
{
    public function __construct(protected FormatterInterface $formatter, protected StdoutLoggerInterface $logger)
    {
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        // 记录异常日志（可选）
        $this->logger->error(sprintf(
            '%s[%s]: %s in %s:%d',
            get_class($throwable),
            $throwable->getCode(),
            $throwable->getMessage(),
            $throwable->getFile(),
            $throwable->getLine()
        ));

        // 区分异常类型，设置不同的错误码
        if ($throwable instanceof BusinessException) {
            $code = $throwable->getCode();
            $message = $throwable->getMessage();
        } elseif ($throwable instanceof ValidationException) {
            $code = 406;
            $allError = $throwable->validator->errors()->all();
            $message = empty($allError) ? '服务器内部错误' : $allError[0];
        } else {
            var_dump($throwable->getCode());
            var_dump($throwable->getMessage());
            // 系统异常默认 500 错误
            $code = 500;
            $message = '服务器内部错误'; // 生产环境建议隐藏具体错误信息
        }

        // 用通用格式包装错误响应
        $result = Result::error($message, $code);
        $body = new SwooleStream(json_encode($result, JSON_UNESCAPED_UNICODE));

        return $response->withHeader('Content-Type', 'application/json')
            ->withBody($body);
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
