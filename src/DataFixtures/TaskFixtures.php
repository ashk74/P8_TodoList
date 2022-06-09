<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TaskFixtures extends Fixture implements DependentFixtureInterface
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

        for ($i = 0; $i < 3; $i++) {
            $task = new Task();
            $task->setTitle('Tâche anonyme')
                ->setContent('Contenu de la tâche')
                ->setCreatedAt(new \DateTime())
                ->setAuthor(null);
            $manager->persist($task);
        }

        for ($i = 0; $i < 9; $i++) {
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
