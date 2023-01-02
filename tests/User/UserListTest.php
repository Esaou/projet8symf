<?php

namespace App\Tests\User;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserListTest extends WebTestCase
{
    public function testList(): void
    {
        $client = static::createClient();
        $client->followRedirects(true);

        // Tentative d'accéder à la page de gestion des utilisateurs sans être connecté
        $client->request('GET', '/admin/users');
        $this->assertEquals('/login', $client->getRequest()->getRequestUri());

        // Tentative d'accéder à la page de gestion des utilisateurs en étant connecté avec le rôle admin
        $testUsers = UserFactory::all();
        $user = null;

        foreach ($testUsers as $testUser) {
            if (in_array("ROLE_ADMIN", $testUser->getRoles())) {
                $user = $testUser;
                break;
            }
        }

        $client->loginUser($user->object());
        $client->request('GET', '/admin/users');
        $this->assertEquals('/admin/users', $client->getRequest()->getRequestUri());
    }
 }