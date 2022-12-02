<?php

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    public const LIST = 'USER_LIST';
    public const EDIT = 'USER_EDIT';
    public const DELETE = 'USER_DELETE';
    public const CREATE = 'USER_CREATE';

    public function __construct(private Security $security)
    {

    }

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::LIST, self::DELETE, self::EDIT, self::CREATE]);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::LIST:
                if (!$user instanceof User) {
                    return false;
                }

                return $this->allowList($user);
            case self::DELETE:
                if (!$user instanceof User) {
                    return false;
                }

                return $this->allowDelete($user);
            case self::EDIT:
                if (!$user instanceof User) {
                    return false;
                }

                return $this->allowEdit($user);
            case self::CREATE:
                return $this->allowCreate();
        }

        return false;
    }

    private function allowCreate(): bool
    {
        if (!$this->security->isGranted('ROLE_USER')) {
            return true;
        }

        return false;
    }

    private function allowList(User $userConnected): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return false;
    }

    private function allowEdit(User $userConnected): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return false;
    }

    private function allowDelete(User $userConnected): bool
    {
        return false;
    }
}
