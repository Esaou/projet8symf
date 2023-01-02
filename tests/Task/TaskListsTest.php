<?php

namespace App\Tests\Task;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskListsTest extends WebTestCase
{
    public function testListAction(): void
    {
        $client = static::createClient();
        $client->followRedirects(true);

        // Tentative d'accéder à la liste des tâches à faire sans être connecté
        $client->request('GET', '/tasks');
        $this->assertEquals('/login', $client->getRequest()->getRequestUri());

        // Tentative d'accéder à la liste des tâches à faire en étant connecté
        $testUser = UserFactory::random();
        $client->loginUser($testUser->object());
        $client->request('GET', '/tasks');
        $this->assertEquals('/tasks', $client->getRequest()->getRequestUri());
    }

    public function testFinishedListAction(): void
    {
        $client = static::createClient();
        $client->followRedirects(true);

        // Tentative d'accéder à la liste des tâches terminées sans être connecté
        $client->request('GET', '/finished-tasks');
        $this->assertEquals('/', $client->getRequest()->getRequestUri());

        // Tentative d'accéder à la liste des tâches terminées en étant connecté
        $testUser = UserFactory::random();
        $client->loginUser($testUser->object());
        $client->request('GET', '/finished-tasks');
        $this->assertEquals('/finished-tasks', $client->getRequest()->getRequestUri());
    }

    public function testExpiredListAction(): void
    {
        $client = static::createClient();
        $client->followRedirects(true);

        // Tentative d'accéder à la liste des tâches expirées sans être connecté
        $client->request('GET', '/expired-tasks');
        $this->assertEquals('/', $client->getRequest()->getRequestUri());

        // Tentative d'accéder à la liste des tâches expirées en étant connecté
        $testUser = UserFactory::random();
        $client->loginUser($testUser->object());
        $client->request('GET', '/expired-tasks');
        $this->assertEquals('/expired-tasks', $client->getRequest()->getRequestUri());
    }
 }