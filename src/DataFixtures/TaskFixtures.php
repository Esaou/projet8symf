<?php

namespace App\DataFixtures;

use App\Factory\TaskFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    private ObjectManager $manager;

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }


    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;

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

            $task = $task->object()->setIsDone($isDone);
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
