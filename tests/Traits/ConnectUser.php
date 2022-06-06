<?php

namespace App\Tests\Traits;

use App\Repository\UserRepository;

trait ConnectUser
{
    /**
     * Retrieve a user and connect him
     *
     * @param bool $isAdmin Select user role
     *
     * @return void
     */
    protected function connectUser(bool $isAdmin = false): void
    {
        $username = $isAdmin ? 'Admin' : 'User1';

        $user = (static::getContainer()->get(UserRepository::class))->findOneByUsername($username);

        $this->client->loginUser($user);
    }
}
