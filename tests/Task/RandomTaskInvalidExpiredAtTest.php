<?php

namespace App\Tests\Task;

use App\Factory\UserFactory;
use App\Repository\TaskRepository;
use Facebook\WebDriver\WebDriverBy;
use Symfony\Component\Panther\PantherTestCase;

class RandomTaskInvalidExpiredAtTest extends PantherTestCase
{
    public function testRandomTaskInvalidExpiredAtAction() {
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

        // Click sur le titre de la première tâche pour l'éditer
        $tasks = self::getContainer()->get(TaskRepository::class)->findByRole($user->object());
        $taskId = current($tasks)->getId();
        $client->waitFor(".taskTitle".$taskId);
        $client->getWebDriver()->findElement(WebDriverBy::className("taskTitle".$taskId))->click();

        $this->assertSelectorTextContains('.updateButton', 'Modifier');

        // Remplissage du formulaire de modification avec une date d'expiration invalide (antérieure)
        $client->getWebDriver()->findElement(WebDriverBy::id('task_expiredAt'))->sendKeys('03/08/00202200:00');

        $client->getWebDriver()->findElement(WebDriverBy::className('updateButton'))->click();
        $this->assertSelectorTextContains('.invalid-feedback', 'Cette valeur doit être supérieure');

        $client->restart();
    }
 }