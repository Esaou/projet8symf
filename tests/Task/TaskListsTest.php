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

        // retrieve the test user
        $testUser = UserFactory::random();

        // simulate $testUser being logged in
        $client->loginUser($testUser->object());

        $client->request('GET', '/tasks');

        $this->assertResponseIsSuccessful();
    }

    public function testFinishedListAction(): void
    {
        $client = static::createClient();
        $client->followRedirects(true);

        $client->request('GET', '/finished-tasks');

        // retrieve the test user
        $testUser = UserFactory::random();

        // simulate $testUser being logged in
        $client->loginUser($testUser->object());

        $client->request('GET', '/finished-tasks');

        $this->assertResponseIsSuccessful();
    }

    public function testExpiredListAction(): void
    {
        $client = static::createClient();
        $client->followRedirects(true);

        $client->request('GET', '/expired-tasks');

        // retrieve the test user
        $testUser = UserFactory::random();

        // simulate $testUser being logged in
        $client->loginUser($testUser->object());

        $client->request('GET', '/expired-tasks');

        $this->assertResponseIsSuccessful();
    }
 }