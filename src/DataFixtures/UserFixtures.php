<?php

namespace App\DataFixtures;

use App\Factory\UserFactory;
use App\Factory\UserrFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
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

        $users = UserFactory::createMany(5);

        $nb = 0;

        foreach ($users as $user) {

            $roles = ['ROLE_USER'];

            if ($nb % 2 == 0) {
                $roles = ['ROLE_ADMIN'];
            }

            $nb++;

            $user = $user->object()->setRoles($roles);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
