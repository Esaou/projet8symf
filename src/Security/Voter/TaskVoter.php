<?php

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class TaskVoter extends Voter
{
    public const CREATE = 'TASK_CREATE';
    public const EDIT = 'TASK_EDIT';
    public const LIST = 'TASK_LIST';
    public const DELETE = 'TASK_DELETE';

    public function __construct(private Security $security)
    {

    }

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::DELETE, self::CREATE, self::LIST])
            && ($subject instanceof Task || null === $subject);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                return $this->allowEdit($subject, $user);

            case self::DELETE:
                return $this->allowDelete($subject, $user);

            case self::CREATE:
                return $this->allowCreate();
            case self::LIST:
                return $this->allowList();
        }

        return false;
    }

    private function allowList(): bool
    {
        // La liste des tâches peut être consulté par un utilisateur connecté
        if ($this->security->isGranted('ROLE_USER')) {
            return true;
        }

        return false;
    }

    private function allowEdit(Task $task, UserInterface $user): bool
    {
        // Un utilisateur peut modifier ses tâches
        if ($task->getUser() === $user) {
            return true;
        }

        // Une tâche anonyme peut être modifiée par ROLE_ADMIN
        if ($task->getUser() === null && $this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return false;
    }

    private function allowCreate(): bool
    {
        // Une tâche peut être créée par un utilisateur connecté
        if ($this->security->isGranted('ROLE_USER')) {
            return true;
        }

        return false;
    }

    private function allowDelete(Task $task, UserInterface $user): bool
    {
        // Un utilisateur peut modifier ses tâches
        if ($task->getUser() === $user) {
            return true;
        }

        // Une tâche anonyme peut être supprimée par ROLE_ADMIN
        if ($task->getUser() === null && $this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return false;
    }
}
