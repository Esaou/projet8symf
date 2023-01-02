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
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class TaskController extends AbstractController
{
    public function __construct(private TranslatorInterface $translator, private SluggerInterface $slugger, private EntityManagerInterface $manager, private TaskRepository $taskRepository)
    {

    }

    #[Route(path: '/tasks', name: 'task_list')]
    public function listAction(): Response
    {
        if (!$this->isGranted(TaskVoter::LIST)) {
            $this->addFlash('error', $this->translator->trans('flash.voters.connect'));
            return $this->redirectToRoute('app_login');
        }

        return $this->render('task/list.html.twig', ['tasks' => $this->taskRepository->findByRole($this->getUser())]);
    }

    #[Route(path: '/finished-tasks', name: 'finished_task_list')]
    public function finishedListAction(): Response
    {
        if (!$this->isGranted(TaskVoter::LIST)) {
            $this->addFlash('error', $this->translator->trans('flash.voters.connect'));
            return $this->redirectToRoute('homepage');
        }

        return $this->render('task/finishedlist.html.twig', ['tasks' => $this->taskRepository->findByRole($this->getUser(), true)]);
    }

    #[Route(path: '/expired-tasks', name: 'expired_task_list')]
    public function expiredListAction(): Response
    {
        if (!$this->isGranted(TaskVoter::LIST)) {
            $this->addFlash('error', $this->translator->trans('flash.voters.connect'));
            return $this->redirectToRoute('homepage');
        }

        return $this->render('task/expiredList.html.twig', ['tasks' => $this->taskRepository->findByRole($this->getUser(), false, true)]);
    }

    #[Route(path: '/tasks/create', name: 'task_create')]
    public function createAction(Request $request): RedirectResponse|Response
    {
        if (!$this->isGranted(TaskVoter::CREATE)) {
            $this->addFlash('error', $this->translator->trans('flash.voters.connect'));
            return $this->redirectToRoute('homepage');
        }

        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();

            $task->setUser($user);
            $task->setSlug(strtolower($this->slugger->slug($task->getTitle())));
            $this->manager->persist($task);
            $this->manager->flush();

            $this->addFlash('success', $this->translator->trans('flash.task.add'));

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route(path: '/tasks/{slug}/edit', name: 'task_edit')]
    public function editAction(string $slug, Request $request): RedirectResponse|Response
    {
        $task = $this->taskRepository->findOneBy(['slug' => $slug]);

        if (!$this->isGranted(TaskVoter::EDIT, $task)) {
            $this->addFlash('error', $this->translator->trans('flash.voters.creator'));
            return $this->redirectToRoute('task_list');
        }

        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();

            $task->setUpdatedAt(new \DateTime());
            $task->setSlug(strtolower($this->slugger->slug($task->getTitle())));

            $this->manager->persist($task);
            $this->manager->flush();

            $this->addFlash('success', $this->translator->trans('flash.task.update'));

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[Route(path: '/tasks/{slug}/toggle', name: 'task_toggle')]
    public function toggleTaskAction(string $slug): RedirectResponse
    {
        $task = $this->taskRepository->findOneBy(['slug' => $slug]);

        if (!$this->isGranted(TaskVoter::EDIT, $task)) {
            $this->addFlash('error', $this->translator->trans('flash.voters.creator'));
            return $this->redirectToRoute('task_list');
        }

        $isDone = $task->getIsDone();

        $task->toggle(!$task->getIsDone());
        $this->manager->flush();

        if ($isDone) {
            $this->addFlash('success', $this->translator->trans('flash.task.todo'));
            return $this->redirectToRoute('finished_task_list');
        }

        $this->addFlash('success', $this->translator->trans('flash.task.do'));

        return $this->redirectToRoute('task_list');
    }

    #[Route(path: '/tasks/{slug}/delete', name: 'task_delete')]
    public function deleteTaskAction(string $slug): RedirectResponse
    {
        $task = $this->taskRepository->findOneBy(['slug' => $slug]);

        if (!$this->isGranted(TaskVoter::DELETE, $task)) {
            $this->addFlash('error', $this->translator->trans('flash.voters.creator'));
            return $this->redirectToRoute('task_list');
        }

        $isDone = $task->getIsDone();

        $this->manager->remove($task);
        $this->manager->flush();

        $this->addFlash('success', $this->translator->trans('flash.task.delete'));

        if ($isDone) {
            return $this->redirectToRoute('finished_task_list');
        }

        return $this->redirectToRoute('task_list');
    }
}
