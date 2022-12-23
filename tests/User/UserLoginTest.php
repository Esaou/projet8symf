<?php

namespace App\Tests\User;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserLoginTest extends WebTestCase
{
    public function testLoginAction(): void
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
        $this->assertEquals('/', $client->getRequest()->getRequestUri());

        $client->restart();

        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form();
        $client->followRedirects(true);

        $form['username'] = "hschiller@collins.net";
        $form['password'] = "Motdepassergpd1!";

        $client->submit($form);

        $this->assertResponseIsSuccessful();
    }
 }