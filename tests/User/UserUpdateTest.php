<?php

namespace App\Tests\User;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserUpdateTest extends WebTestCase
{
    public function testUpdateAction() {
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

        // Tentative de modification de rôle utilisateur sans être connecté
        $client->request('GET', '/admin/users/'.$user->getUuid().'/role/switch');
        $this->assertEquals('/', $client->getRequest()->getRequestUri());

        // Tentative de modification de rôle d'un utilisateur en étant connecté en tant qu'admin
        $client->loginUser($user->object());

        $userToSwitch = null;

        foreach ($testUsers as $testUser) {
            if (in_array("ROLE_USER", $testUser->getRoles())) {
                $userToSwitch = $testUser;
                break;
            }
        }

        $client->request('GET', '/admin/users/'.$userToSwitch->getUuid().'/role/switch');
        $this->assertEquals('/admin/users', $client->getRequest()->getRequestUri());

        // Cas où l'utilisateur modifie son propre rôle
        $client->request('GET', '/admin/users/'.$user->getUuid().'/role/switch');
    }
 }