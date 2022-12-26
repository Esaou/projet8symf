<?php

namespace App\Tests\Task;

use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Facebook\WebDriver\WebDriverBy;
use Symfony\Component\Panther\PantherTestCase;

class RandomTaskToggleTest extends PantherTestCase
{
    public function testRandomTaskToogleAction() {
        $client = self::createPantherClient([
            'browser' => PantherTestCase::CHROME,
        ]);
        $client->manage()->window()->maximize();

        $crawler = $client->request('GET', '/login');
        sleep(2);
        $form = $crawler->selectButton('Se connecter')->form([
            'username' => "dell41@nolan.org",
            'password' => "Motdepassergpd1!",
        ]);
        $client->submit($form);
        $this->assertSelectorTextContains('.headerTitle', 'Bienvenue sur Todo List');

        sleep(2);
        $client->getWebDriver()->findElement(WebDriverBy::linkText('Tâches à faire'))->click();
        $this->assertSelectorTextContains('.headerTitle', 'Tâches à faire');

        sleep(2);
        $user = self::getContainer()->get(UserRepository::class)->findOneBy(['email' => 'dell41@nolan.org']);
        $tasks = self::getContainer()->get(TaskRepository::class)->findByRole($user);
        $taskId = current($tasks)->getId();
        $client->waitFor(".taskToggleLink".$taskId);
        $client->getWebDriver()->findElement(WebDriverBy::className("taskToggleLink".$taskId))->click();

        $this->assertSelectorTextContains('.headerTitle', 'Tâches à faire');
        $this->assertSelectorNotExists(".taskToggleLink".$taskId);

        sleep(2);
        $client->close();
    }
 }