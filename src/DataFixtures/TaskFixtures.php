<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class TaskFixtures extends Fixture
{
    private UserRepository $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        $users = $this->userRepo->findAll();

        for ($i = 0; $i < 10; $i++) {
            $task = (new Task)
                ->setTitle($faker->sentence(mt_rand(2, 4)))
                ->setContent($faker->paragraph(mt_rand(2, 4)))
                ->setCreatedAt(new \DateTime())
                ->setAuthor($faker->randomElement($users));

            $manager->persist($task);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
