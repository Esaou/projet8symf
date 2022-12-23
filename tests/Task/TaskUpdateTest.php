<?php

namespace App\Tests\Task;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskUpdateTest extends WebTestCase
{
    public function testUpdateAction() {
        $client = static::createClient();
        $client->followRedirects(true);

        // retrieve the test user
        $testUsers = UserFactory::all();

        $testUser = null;

        foreach ($testUsers as $user) {

            if (!empty($user->getTasks()->toArray())) {
                $testUser = $user;
                break;
            }
        }

        $client->request('GET', '/tasks/'.$testUser->getTasks()->first()->getSlug().'/edit');

        $this->assertEquals('/login', $client->getRequest()->getRequestUri());

        // simulate $testUser being logged in
        $client->loginUser($testUser->object());

        $crawler = $client->request('GET', '/tasks/'.$testUser->getTasks()->first()->getSlug().'/edit');

        $form = $crawler->selectButton('Modifier')->form([
            'task[title]' => "Titre d'exemple",
            'task[content]' => "Contenu d'exemple",
            'task[expiredAt]' => "2023-03-08 00:00:00",
        ]);

        $client->submit($form);

        $this->assertResponseIsSuccessful();
    }
 }