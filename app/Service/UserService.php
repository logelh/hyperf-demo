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

namespace App\Service;

use App\Repository\UserProfileRepository;

class UserService
{
    public function __construct(protected UserProfileRepository $userProfileRepository)
    {
    }

    public function profile($uid)
    {
        return $this->userProfileRepository->getByUid($uid);
    }

    public function updateProfile(array $profileData)
    {
        $this->userProfileRepository->updateByUid($profileData);

        $this->userProfileRepository->deleteCacheByUid($profileData['user_id']);
    }
}
