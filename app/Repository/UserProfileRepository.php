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

namespace App\Repository;

use App\Model\UserProfile;
use Hyperf\Di\Annotation\Inject;

class UserProfileRepository
{
    #[Inject]
    protected UserProfile $userProfile;

    public function getById($uid)
    {
        return $this->userProfile::where('user_id', $uid)->createOrFirst(['user_id' => $uid]);
    }
}
