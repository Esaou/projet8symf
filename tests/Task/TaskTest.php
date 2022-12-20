<?php

namespace App\Tests\Task;

use App\Entity\Task;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Validator\ConstraintViolation;

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

    public function assertHasErrors(Task $task, int $number = 0)
    {
        self::bootKernel();

        $errors = self::getContainer()->get('validator')->validate($task);

        $messages = [];
        /** @var ConstraintViolation $error */
        foreach($errors as $error) {
            $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }

        $this->assertCount($number, $errors, implode(', ', $messages));
    }

    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

    public function testInvalidExpiredAt()
    {
        $this->assertHasErrors($this->getEntity()->setExpiredAt(new \DateTime('yesterday')), 1);
    }

    public function testBlankTitle()
    {
        $this->assertHasErrors($this->getEntity()->setTitle(''), 2);
    }
}