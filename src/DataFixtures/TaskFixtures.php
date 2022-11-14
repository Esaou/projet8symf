<?php

namespace App\DataFixtures;

use App\Factory\TaskFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private SluggerInterface $slugger)
    {
    }


    public function load(ObjectManager $manager): void
    {
        $tasks = TaskFactory::createMany(
            40,
            function() {
                return ['user' => UserFactory::random()];
            }
        );

        $nb = 0;

        foreach ($tasks as $task) {

            $isDone = true;

            if ($nb % 2 == 0) {
                $isDone = false;
            }

            $nb++;

            $task = $task->setIsDone($isDone);

            if ($nb % 2 == 0) {
                $task = $task->setUser(null);
            }

            $manager->persist($task);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class
        ];
    }
}
