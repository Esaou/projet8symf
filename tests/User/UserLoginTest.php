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

        // Tentative d'accéder à la page de login en étant déjà connecté
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

        // Tentative d'accéder à la page de login sans être connecté
        $client->restart();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form([
            'username' => "dell41@nolan.org",
            'password' => "Motdepassergpd1!",
        ]);

        $client->submit($form);
        $this->assertSelectorTextContains('.headerTitle', 'Bienvenue sur Todo List');
    }
 }