<?php

namespace App\Tests\Task;

use App\Entity\Task;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class TaskTest extends WebTestCase
{
    public function getEntity(): Task
    {
        $testUser = UserFactory::random();

        return (new Task())
            ->setTitle("Titre d'exemple")
            ->setContent('Contenu de test')
            ->setUser($testUser->object())
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime())
            ->setIsDone(false)
            ->setSlug('titre-d-exemple')
            ->setExpiredAt(new \DateTime('+1 day'));
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

    public function testInvalidExpiredAt()
    {
        self::bootKernel();

        $errors = self::getContainer()->get('validator')->validate($this->getEntity()->setExpiredAt(new \DateTime('yesterday')));

        if ($this->constraintExist($errors, GreaterThan::class)) {
            $this->assertTrue(true);
        } else {
            $this->fail(true);
        }
    }

    public function testBlankTitle()
    {
        self::bootKernel();

        $errors = self::getContainer()->get('validator')->validate($this->getEntity()->setTitle(''));

        if ($this->constraintExist($errors, NotBlank::class)) {
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