<?php

namespace App\Repository;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Task>
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private Security $security)
    {
        parent::__construct($registry, Task::class);
    }

    public function findByRole(UserInterface $user)
    {
        $query = $this->createQueryBuilder(Task::ALIAS)
                    ->setParameter('userId', $user->getId());

        if ($this->security->isGranted('ROLE_ADMIN')) {
            $query
                ->where(Task::ALIAS.'.user = :userId')
                ->orWhere(Task::ALIAS.'.user is null');
        } elseif ($this->security->isGranted('ROLE_USER')) {
            $query
                ->where(Task::ALIAS.'.user = :userId');
        }

        return $query
            ->getQuery()
            ->getResult();
    }
}