<?php
/**
 * User service
 */

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;

/**
 * Class UserService
 */
class UserService
{
    /**
     * User repository
     *
     * @var UserRepository User repository
     */
    private UserRepository $userRepository;

    /**
     * Construct new service
     *
     * @param UserRepository $userRepository User repository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Save user
     *
     * @param User $user User
     *
     * @return void Void
     */
    public function save(User $user): void
    {
        $this->userRepository->save($user);
    }
}
