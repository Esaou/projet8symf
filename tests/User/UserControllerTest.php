<?php

namespace App\Tests\User;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testUserLogin(): void
    {
        $client = static::createClient();
        $client->followRedirects(true);

        $testUsers = UserFactory::all();

        $user = null;

        foreach ($testUsers as $testUser) {
            if (in_array("ROLE_ADMIN", $testUser->getRoles())) {
                $user = $testUser;
                break;
            }
        }

        $client->loginUser($user->object());
        $client->request('GET', '/login');

        $client->restart();

        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form();
        $client->followRedirects(true);

        $form['username'] = "Username";
        $form['password'] = "Motdepassergpd1!";

        $client->submit($form);

        $this->assertResponseIsSuccessful();
    }

    public function testUserList(): void
    {
        $client = static::createClient();
        $client->followRedirects(true);
        $client->request('GET', '/admin/users');

        // retrieve the test user
        $testUsers = UserFactory::all();

        $user = null;

        foreach ($testUsers as $testUser) {
            if (in_array("ROLE_ADMIN", $testUser->getRoles())) {
                $user = $testUser;
                break;
            }
        }

        // simulate $testUser being logged in
        $client->loginUser($user->object());

        $client->request('GET', '/admin/users');

        $this->assertResponseIsSuccessful();
    }

    public function testAddUser() {
        $client = static::createClient();
        $client->followRedirects(true);
        $client->request('GET', '/create/user');

        $testUsers = UserFactory::all();

        $user = null;

        foreach ($testUsers as $testUser) {
            if (in_array("ROLE_ADMIN", $testUser->getRoles())) {
                $user = $testUser;
                break;
            }
        }

        $client->loginUser($user->object());
        $client->restart();

        $crawler = $client->request('GET', '/create/user');

        $form = $crawler->selectButton('Ajouter')->form();
        $client->followRedirects(true);

        $form['user[username]'] = "Username";
        $form['user[email]'] = "test@test.com";
        $form['user[password][first]'] = "Motdepassergpd1!";
        $form['user[password][second]'] = "Motdepassergpd1!";
        $form['user[roles]'] = "ROLE_ADMIN";

        $client->submit($form);

        $this->assertResponseIsSuccessful();
    }

    public function testEditUser() {
        $client = static::createClient();
        $client->followRedirects(true);

        $testUsers = UserFactory::all();

        $user = null;

        foreach ($testUsers as $testUser) {
            if (in_array("ROLE_ADMIN", $testUser->getRoles())) {
                $user = $testUser;
                break;
            }
        }

        $client->request('GET', '/admin/users/'.$user->getUuid().'/role/switch');


        $client->loginUser($user->object());

        $client->request('GET', '/admin/users/'.$user->getUuid().'/role/switch');


        $this->assertResponseIsSuccessful();
    }
 }