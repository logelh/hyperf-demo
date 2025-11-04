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
use Hyperf\Cache\Annotation\Cacheable;
use Hyperf\Cache\Listener\DeleteListenerEvent;
use Hyperf\Di\Annotation\Inject;
use Psr\EventDispatcher\EventDispatcherInterface;

class UserProfileRepository
{
    #[Inject]
    protected UserProfile $userProfile;

    #[Inject]
    protected EventDispatcherInterface $dispatcher;

    #[Cacheable(prefix: 'user:profile', ttl: 1200, listener: 'profile-update')]
    public function getByUid($uid)
    {
        return $this->userProfile::where('user_id', $uid)->createOrFirst(['user_id' => $uid]);
    }

    public function deleteCacheByUid($uid)
    {
        $this->dispatcher->dispatch(new DeleteListenerEvent('profile-update', [$uid]));
    }

    public function updateByUid(array $profileData)
    {
        $this->userProfile::where(['user_id' => $profileData['user_id']])->update($profileData);
    }
}
