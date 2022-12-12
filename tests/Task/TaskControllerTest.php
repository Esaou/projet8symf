<?php

namespace App\Tests\Task;

use App\Entity\User;
use App\Factory\TaskFactory;
use App\Factory\UserFactory;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;

class TaskControllerTest extends WebTestCase
{
    public function testTaskList(): void
    {
        $client = static::createClient();
        $client->followRedirects(true);

        $client->request('GET', '/tasks');
        $client->request('GET', '/finished-tasks');
        $client->request('GET', '/expired-tasks');

        // retrieve the test user
        $testUser = UserFactory::random();

        // simulate $testUser being logged in
        $client->loginUser($testUser->object());

        $client->request('GET', '/tasks');
        $client->request('GET', '/finished-tasks');
        $client->request('GET', '/expired-tasks');

        $this->assertResponseIsSuccessful();
    }
    
    public function testAddTask() {
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

    public function testEditTask() {
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

        // simulate $testUser being logged in
        $client->loginUser($testUser->object());

        $crawler = $client->request('GET', '/tasks/'.$testUser->getTasks()->first()->getSlug().'/edit');

        $form = $crawler->selectButton('Modifier')->form();

        $form['task[title]'] = "Titre d'exemple";
        $form['task[content]'] = "Contenu d'exemple";
        $form['task[expiredAt]'] = "2022-03-08";

        $client->submit($form);

        $this->assertResponseIsSuccessful();
    }

    public function testToggleTask() {
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

        // simulate $testUser being logged in
        $client->loginUser($testUser->object());

        $client->request('GET', '/tasks/'.$testUser->getTasks()->first()->getSlug().'/toggle');

        $this->assertResponseIsSuccessful();
    }

    public function testDeleteTask() {
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