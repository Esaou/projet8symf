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
 }