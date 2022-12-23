<?php

namespace App\Tests\User;

use App\Entity\Task;
use App\Entity\User;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserTest extends WebTestCase
{
    public function getEntity(): User
    {
        return (new User())
            ->setUsername("Exemple")
            ->setEmail('exemple@exemple.com')
            ->setRoles(["ROLE_ADMIN"])
            ->setPassword('Motdepassergpd1!')
            ->setUuid(Uuid::v6());
    }

    public function testValidEntity()
    {
        self::bootKernel();

        $errors = self::getContainer()->get('validator')->validate($this->getEntity());

        if ($this->constraintExist($errors)) {
            $this->fail(true);
        } else {
            $this->assertTrue(true);
        }
    }

    public function testInvalidPassword()
    {
        self::bootKernel();

        /** @var UserPasswordHasherInterface $userPasswordHasherInterface */
        $userPasswordHasherInterface = self::getContainer()->get(UserPasswordHasherInterface::class);
        $password = $userPasswordHasherInterface->hashPassword($this->getEntity(), 'test');

        $errors = self::getContainer()->get('validator')->validate($this->getEntity()->setPassword($password));

        if ($this->constraintExist($errors, Regex::class)) {
            $this->assertTrue(true);
        } elseif (!$this->constraintExist($errors, Regex::class)) {
            $this->fail(true);
        }
    }

    public function testInvalidEmail()
    {
        self::bootKernel();

        $errors = self::getContainer()->get('validator')->validate($this->getEntity()->setEmail('incorrect@email'));

        if ($this->constraintExist($errors, Email::class)) {
            $this->assertTrue(true);
        } else {
            $this->fail(true);
        }
    }

    private function constraintExist($errors, $class = null): bool
    {
        foreach ($errors as $error) {
            if ($error->getConstraint() instanceof $class) {
                return true;
            }
        }

        return false;
    }
}