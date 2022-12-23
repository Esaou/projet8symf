<?php

namespace App\Tests\Task;

use App\Factory\UserFactory;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskToggleTest extends WebTestCase
{
    public function testToggleAction() {
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

        $client->request('GET', '/tasks/'.$testUser->getTasks()->first()->getSlug().'/toggle');

        $this->assertEquals('/login', $client->getRequest()->getRequestUri());

        // simulate $testUser being logged in
        $client->loginUser($testUser->object());

        $testTask = null;

        foreach ($testUser->getTasks() as $task) {
            if (false === $task->getIsDone()) {
                $testTask = $task;
                break;
            }
        }

        $client->request('GET', '/tasks/'.$testTask->getSlug().'/toggle');

        foreach ($testUser->getTasks() as $task) {
            if (true === $task->getIsDone()) {
                $testTask = $task;
                break;
            }
        }

        $client->request('GET', '/tasks/'.$testTask->getSlug().'/toggle');

        $this->assertResponseIsSuccessful();
    }
 }