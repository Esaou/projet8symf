<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Security\Voter\TaskVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class TaskController extends AbstractController
{
    private UserInterface $user;

    public function __construct(private Security $security, private EntityManagerInterface $manager, private TaskRepository $taskRepository)
    {
        $this->user = $this->security->getUser();
    }

    #[Route(path: '/tasks', name: 'task_list')]
    public function listAction(): Response
    {
        return $this->render('task/list.html.twig', ['tasks' => $this->taskRepository->findByRole($this->user)]);
    }

    #[Route(path: '/finished-tasks', name: 'finished_task_list')]
    public function finishedListAction(): Response
    {
        return $this->render('task/finishedlist.html.twig', ['tasks' => $this->taskRepository->findByRole($this->user, true)]);
    }

    #[Route(path: '/tasks/create', name: 'task_create')]
    public function createAction(Request $request): RedirectResponse|Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var User $user */
            $user = $this->getUser();

            $task->setUser($user);
            $this->manager->persist($task);
            $this->manager->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route(path: '/tasks/{id}/edit', name: 'task_edit')]
    public function editAction(Task $task, Request $request): RedirectResponse|Response
    {
        if (!$this->isGranted(TaskVoter::EDIT, $task)) {
            return $this->redirectToRoute('task_list');
        }

        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[Route(path: '/tasks/{id}/toggle', name: 'task_toggle')]
    public function toggleTaskAction(Task $task): RedirectResponse
    {
        if (!$this->isGranted(TaskVoter::EDIT, $task)) {
            return $this->redirectToRoute('task_list');
        }

        $task->toggle(!$task->getIsDone());
        $this->manager->flush();

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

        if (false === $task->getIsDone()) {
            return $this->redirectToRoute('finished_task_list');
        }

        return $this->redirectToRoute('task_list');
    }

    #[Route(path: '/tasks/{task}/delete', name: 'task_delete')]
    public function deleteTaskAction(Task $task): RedirectResponse
    {
        if (!$this->isGranted(TaskVoter::DELETE, $task)) {
            return $this->redirectToRoute('task_list');
        }

        $this->manager->remove($task);
        $this->manager->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }
}
