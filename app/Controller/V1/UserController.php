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
use App\Request\UserProfileRequest;
use App\Service\UserService;
use Hyperf\Di\Annotation\Inject;

class UserController extends AbstractController
{

    #[Inject]
    protected UserService $userService;

    public function profile()
    {
        /** @var User $userInfo */
        $userInfo = $this->request->getAttribute('user');
        return Result::success($this->userService->profile($userInfo->id));
    }

    public function profileUpdate(UserProfileRequest $request)
    {
        /** @var User $userInfo */
        $userInfo = $this->request->getAttribute('user');
        $validated = $request->validated();
        $validated['user_id'] = $userInfo->id;

        $this->userService->updateProfile($validated);

        return Result::success();
    }
}
