<?php

namespace App\Tests\Task;

use App\Factory\UserFactory;
use App\Repository\TaskRepository;
use Facebook\WebDriver\Cookie;
use Facebook\WebDriver\WebDriverBy;
use Symfony\Component\Panther\PantherTestCase;

class RandomTaskToggleTest extends PantherTestCase
{
    public function testRandomTaskToogleAction() {
        $client = self::createPantherClient([
            'browser' => PantherTestCase::CHROME,
        ]);
        $client->manage()->window()->maximize();

        $user = UserFactory::random();

        // Connexion
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            'username' => $user->getEmail(),
            'password' => "Motdepassergpd1!",
        ]);

        $client->submit($form);
        $this->assertSelectorTextContains('.headerTitle', 'Bienvenue sur Todo List');

        // Click sur le bouton "Tâches à faire"
        $client->getWebDriver()->findElement(WebDriverBy::linkText('Tâches à faire'))->click();
        $this->assertSelectorTextContains('.headerTitle', 'Tâches à faire');

        // Click sur le bouton "Marquer comme faite" de la première tâche
        $tasks = self::getContainer()->get(TaskRepository::class)->findByRole($user->object());
        $taskId = current($tasks)->getId();
        $client->waitFor(".taskToggleLink".$taskId);
        $client->getWebDriver()->findElement(WebDriverBy::className("taskToggleLink".$taskId))->click();

        $this->assertSelectorTextContains('.headerTitle', 'Tâches à faire');
        $this->assertSelectorNotExists(".taskToggleLink".$taskId);

        $client->close();
    }
 }