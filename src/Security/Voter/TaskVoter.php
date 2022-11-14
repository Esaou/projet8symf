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
    public const EDIT = 'TASK_EDIT';
    public const DELETE = 'TASK_DELETE';

    public function __construct(private Security $security)
    {

    }

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof Task;
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
