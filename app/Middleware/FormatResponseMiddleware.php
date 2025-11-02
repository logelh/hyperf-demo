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

namespace App\Middleware;

use App\Common\Result;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class FormatResponseMiddleware implements MiddlewareInterface
{
    public function __construct(
        private ContainerInterface $container,
        private HttpResponse $response
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // 执行后续处理（如控制器方法），获取原始响应
        $response = $handler->handle($request);

        // 如果是已经包装过的响应（如通过 Result 返回的数组），直接处理
        if ($response->getBody()->isSeekable()) {
            $response->getBody()->rewind();
            $content = $response->getBody()->getContents();
            $response->getBody()->rewind();

            // 尝试解析原始响应内容
            $data = json_decode($content, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                // 如果已经是标准格式（包含 code/message/data/timestamp），直接返回
                if (isset($data['code'], $data['message'], $data['timestamp'])) {
                    return $response;
                }

                // 否则，用 Result 包装原始数据（默认视为成功响应）
                $formatted = Result::success($data);
                return $this->response->json($formatted);
            }
        }

        // 非 JSON 响应直接返回（如文件下载等特殊场景）
        return $response;
    }
}
