<?php

namespace App\Tests\Task;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskCreateTest extends WebTestCase
{
    public function testCreateAction() {
        $client = static::createClient();
        $client->followRedirects(true);
        $client->request('GET', '/tasks/create');


        // retrieve the test user
        $testUser = UserFactory::random();

        // simulate $testUser being logged in
        $client->loginUser($testUser->object());

        $client->request('GET', '/tasks/create');

        $client->submitForm('Ajouter', [
            'task[title]' => "Titre d'exemple",
            'task[content]' => "Contenu d'exemple",
            'task[expiredAt]' => "2022-03-08",
        ]);

        $this->assertResponseIsSuccessful();
    }
 }