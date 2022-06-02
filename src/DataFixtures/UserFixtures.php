<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        $user = new User();
        $user->setEmail('johndoe@email.com')
            ->setUsername('John.Doe')
            ->setPassword($this->passwordHasher->hashPassword($user, 'password'));
        $manager->persist($user);

        for ($i = 0; $i < 9; $i++) {
            $user = new User();
            $user->setEmail($faker->freeEmail())
                ->setUsername($faker->userName())
                ->setPassword($this->passwordHasher->hashPassword($user, '123'));

            $manager->persist($user);
        }
        $manager->flush();
    }
}
