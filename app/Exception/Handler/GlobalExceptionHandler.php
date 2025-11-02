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

use App\Exception\BusinessException; // 自定义业务异常（见步骤3）
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\ExceptionHandler\Formatter\FormatterInterface;
use Hyperf\HttpMessage\Exception\HttpException;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Validation\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

use function Hyperf\Support\env;

class GlobalExceptionHandler extends ExceptionHandler
{
    public function __construct(protected FormatterInterface $formatter, protected StdoutLoggerInterface $logger)
    {
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        // 1. 定义默认响应结构
        $data = [
            'code' => 500, // 默认错误码
            'message' => '服务器内部错误', // 默认错误信息
            'data' => null,
        ];

        // 2. 处理不同类型的异常
        if ($throwable instanceof BusinessException) {
            // 业务异常：使用自定义的错误码和信息
            $data['code'] = $throwable->getCode();
            $data['message'] = $throwable->getMessage();
        } elseif ($throwable instanceof ValidationException) {
            // 验证器异常：提取验证错误信息
            $data['code'] = 422; // 通常用422表示验证失败
            $data['message'] = '参数验证失败';
            $data['data'] = $throwable->validator->errors()->all(); // 具体错误信息

            $this->stopPropagation();
            return $response->withStatus(500)->withBody(new SwooleStream(json_encode($data)));
        } elseif ($this->isHttpException($throwable)) {
            // HTTP异常（如404、403等）
            $statusCode = $throwable->getStatusCode();
            $data['code'] = $statusCode;
            $data['message'] = $throwable->getMessage() ?: '请求错误';
        } else {
            // 其他未捕获的异常：记录日志（生产环境避免暴露详细信息）
            $this->logger->error($this->formatter->format($throwable));
            // 生产环境可保持默认信息，开发环境可显示详细错误
            if (env('APP_ENV') === 'dev') {
                $data['message'] = $throwable->getMessage();
                $data['data'] = [
                    'file' => $throwable->getFile(),
                    'line' => $throwable->getLine(),
                    'trace' => explode("\n", $throwable->getTraceAsString()),
                ];
            }
        }

        // 3. 构建响应
        $body = json_encode($data, JSON_UNESCAPED_UNICODE);
        //        var_dump($throwable);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($this->getStatusCode($throwable, $data['code'])) // 设置HTTP状态码
            ->withBody(new SwooleStream($body));
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }

    /**
     * 确定HTTP响应状态码
     */
    private function getStatusCode(Throwable $throwable, int $defaultCode): int
    {
        // HTTP异常直接使用其状态码
        if ($this->isHttpException($throwable)) {
            return $throwable->getStatusCode();
        }
        // 业务异常通常返回200（状态码200，错误码非0），也可根据需求调整
        if ($throwable instanceof BusinessException) {
            return 200;
        }
        // 验证器异常通常返回422
        if ($throwable instanceof ValidationException) {
            return 422;
        }
        return $defaultCode;
    }

    /**
     * 判断是否为HTTP异常.
     */
    private function isHttpException(Throwable $throwable): bool
    {
        return $throwable instanceof HttpException;
    }
}
