<?php

namespace App\Tests\Task;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskUpdateTest extends WebTestCase
{
    public function testUpdateAction() {
        $client = static::createClient();
        $client->followRedirects(true);

        $testUsers = UserFactory::all();
        $testUser = null;

        foreach ($testUsers as $user) {
            if (!empty($user->getTasks()->toArray())) {
                $testUser = $user;
                break;
            }
        }

        // Tentative de modifier une tâche sans être connecté
        $client->request('GET', '/tasks/'.$testUser->getTasks()->first()->getSlug().'/edit');
        $this->assertEquals('/login', $client->getRequest()->getRequestUri());

        // Tentative de modifier une tâche en étant connecté
        $client->loginUser($testUser->object());
        $crawler = $client->request('GET', '/tasks/'.$testUser->getTasks()->first()->getSlug().'/edit');

        $form = $crawler->selectButton('Modifier')->form([
            'task[title]' => "Titre d'exemple",
            'task[content]' => "Contenu d'exemple",
            'task[expiredAt]' => "2023-03-08 00:00:00",
        ]);

        $client->submit($form);
        $this->assertEquals('/tasks', $client->getRequest()->getRequestUri());
    }
 }