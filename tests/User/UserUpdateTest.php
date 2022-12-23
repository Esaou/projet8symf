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

        // Tentative d'update sans être connecté

        $client->request('GET', '/admin/users/'.$user->getUuid().'/role/switch');
        $this->assertEquals('/', $client->getRequest()->getRequestUri());

        // Tentative d'update en étant connecté

        $client->loginUser($user->object());

        $userToSwitch = null;

        foreach ($testUsers as $testUser) {
            if (in_array("ROLE_USER", $testUser->getRoles())) {
                $userToSwitch = $testUser;
                break;
            }
        }

        $client->request('GET', '/admin/users/'.$userToSwitch->getUuid().'/role/switch');

        // Second appel pour vérifier l'entièreté de l'execution de la méthode.

        $client->request('GET', '/admin/users/'.$user->getUuid().'/role/switch');

        $this->assertResponseIsSuccessful();
    }
 }