<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Security\Voter\TaskVoter;
use App\Security\Voter\UserVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class UserController extends AbstractController
{
    public function __construct(private TokenStorageInterface $tokenStorage, private Security $security, private EntityManagerInterface $manager,private UserRepository $userRepository, private UserPasswordHasherInterface $passwordHasher)
    {

    }

    #[Route(path: '/admin/users', name: 'user_list')]
    public function listAction(): Response
    {
        if (!$this->isGranted(UserVoter::LIST)) {
            return $this->redirectToRoute('homepage');
        }

        $this->denyAccessUnlessGranted(UserVoter::LIST);
        return $this->render('user/list.html.twig', ['users' => $this->userRepository->findAll()]);
    }

    #[Route(path: '/create/user', name: 'user_create')]
    public function createAction(Request $request): RedirectResponse|Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($password);
            $user->setRoles([$form->get('roles')->getData()]);

            $this->manager->persist($user);
            $this->manager->flush();

            $this->addFlash('success', "L'utilisateur a bien été ajouté.");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route(path: '/admin/users/{user}/role/switch', name: 'user_role_switch')]
    public function editRoleAction(User $user): RedirectResponse
    {
        if (!$this->isGranted(UserVoter::EDIT)) {
            return $this->redirectToRoute('homepage');
        }

        $roles = $user->getRoles();

        foreach ($roles as $role) {
            if ('ROLE_ADMIN' === $role) {
                $user->setRoles(['ROLE_USER']);
            } elseif ('ROLE_USER' === $role) {
                $user->setRoles(['ROLE_ADMIN']);
            }
        }

        $this->manager->persist($user);
        $this->manager->flush();

        $this->addFlash('success', "Rôle modifié avec succès.");

        if ($this->security->getUser() === $user) {
            $this->refreshToken($user);
        }

        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('homepage');
        }

        return $this->redirectToRoute('user_list');
    }

    /**
     * Use this method to refresh token roles immediately
     */
    private function refreshToken(UserInterface $user): void
    {
        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
        $this->tokenStorage->setToken($token);
    }
}
