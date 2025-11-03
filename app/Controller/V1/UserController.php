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

namespace App\Controller\V1;

use App\Common\Result;
use App\Controller\AbstractController;
use App\Model\User;
use App\Service\UserService;

class UserController extends AbstractController
{
    public function __construct(protected UserService $userService)
    {
        parent::__construct();
    }

    public function profile()
    {
        /** @var User $userInfo */
        $userInfo = $this->request->getAttribute('user');
        return Result::success($this->userService->profile($userInfo->id));
    }

}
