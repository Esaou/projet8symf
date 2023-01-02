<?php

namespace App\Tests\Task;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskDeleteTest extends WebTestCase
{
    public function testDeleteAction() {
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

        // Tentative de supprimer une tâche sans être connecté
        $client->request('GET', '/tasks/'.$testUser->getTasks()->first()->getSlug().'/delete');
        $this->assertEquals('/login', $client->getRequest()->getRequestUri());

        // Tentative de supprimer une tâche à faire en étant connecté
        $client->loginUser($testUser->object());

        $testTask = null;

        foreach ($testUser->getTasks() as $task) {
            if (false === $task->getIsDone()) {
                $testTask = $task;
                break;
            }
        }

        $client->request('GET', '/tasks/'.$testTask->getSlug().'/delete');
        $this->assertEquals('/tasks', $client->getRequest()->getRequestUri());

        // Tentative de supprimer une tâche terminée en étant connecté
        foreach ($testUser->getTasks() as $task) {
            if (true === $task->getIsDone()) {
                $testTask = $task;
                break;
            }
        }

        $client->request('GET', '/tasks/'.$testTask->getSlug().'/delete');
        $this->assertEquals('/finished-tasks', $client->getRequest()->getRequestUri());
    }
 }