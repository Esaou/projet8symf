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

        $client->request('GET', '/tasks');

        $this->assertEquals('/login', $client->getRequest()->getRequestUri());

        // retrieve the test user
        $testUser = UserFactory::random();

        // simulate $testUser being logged in
        $client->loginUser($testUser->object());

        $client->request('GET', '/tasks');

        $this->assertEquals('/tasks', $client->getRequest()->getRequestUri());
    }

    public function testFinishedListAction(): void
    {
        $client = static::createClient();
        $client->followRedirects(true);

        $client->request('GET', '/finished-tasks');

        $this->assertEquals('/', $client->getRequest()->getRequestUri());

        // retrieve the test user
        $testUser = UserFactory::random();

        // simulate $testUser being logged in
        $client->loginUser($testUser->object());

        $client->request('GET', '/finished-tasks');

        $this->assertEquals('/finished-tasks', $client->getRequest()->getRequestUri());
    }

    public function testExpiredListAction(): void
    {
        $client = static::createClient();
        $client->followRedirects(true);

        $client->request('GET', '/expired-tasks');

        $this->assertEquals('/', $client->getRequest()->getRequestUri());

        // retrieve the test user
        $testUser = UserFactory::random();

        // simulate $testUser being logged in
        $client->loginUser($testUser->object());

        $client->request('GET', '/expired-tasks');

        $this->assertEquals('/expired-tasks', $client->getRequest()->getRequestUri());
    }
 }