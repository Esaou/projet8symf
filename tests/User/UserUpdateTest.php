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

        $client->request('GET', '/admin/users/'.$user->getUuid().'/role/switch');


        $client->loginUser($user->object());

        $client->request('GET', '/admin/users/'.$user->getUuid().'/role/switch');


        $this->assertResponseIsSuccessful();
    }
 }