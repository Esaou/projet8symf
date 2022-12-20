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

        $client->request('GET', '/tasks/'.$testUser->getTasks()->first()->getSlug().'/delete');

        // simulate $testUser being logged in
        $client->loginUser($testUser->object());

        $client->request('GET', '/tasks/'.$testUser->getTasks()->first()->getSlug().'/delete');

        $this->assertResponseIsSuccessful();
    }
 }