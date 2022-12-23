<?php

namespace App\Tests\User;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserCreateTest extends WebTestCase
{
    public function testCreateAction() {
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
        $client->request('GET', '/create/user');

        $this->assertEquals('/', $client->getRequest()->getRequestUri());

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
 }