<?php

namespace App\Tests\Task;

use App\Factory\UserFactory;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskCreateTest extends WebTestCase
{
    public function testCreateAction() {
        $client = static::createClient();
        $client->followRedirects(true);

        // Tentative pour accéder à la page de création sans être connecté
        $client->request('GET', '/tasks/create');
        $this->assertEquals('/', $client->getRequest()->getRequestUri());

        // Tentative pour accéder à la page de création en étant connecté
        $testUser = UserFactory::random();
        $client->loginUser($testUser->object());

        $crawler = $client->request('GET', '/tasks/create');

        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => "Titre d'exemple",
            'task[content]' => "Contenu d'exemple",
            'task[expiredAt]' => "2023-03-08 00:00:00",
        ]);

        $client->submit($form);

        $createdTask = self::getContainer()->get(TaskRepository::class)->findOneBy(['title' => "Titre d'exemple"]);

        if (null !== $createdTask) {
            $this->assertTrue(true);
        } else {
            $this->fail(true);
        }
    }
 }