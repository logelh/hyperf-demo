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

namespace HyperfTest\Cases;

use Hyperf\Testing\TestCase;

/**
 * 默认指定某个文件测试
 * composer test -- test/Cases/TestUser.php.
 * @internal
 * @coversNothing
 */
class TestUser extends TestCase
{
    public function testIndex()
    {
        $response = $this->get('/');
        var_dump('abc');
        $response->assertOk()->assertSee('Hyperf');
    }

    public function testLogin()
    {
        var_dump('ddd');
    }
}
