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

    public function findByRole(UserInterface $user, bool $isDone = false, bool $isExpired = false)
    {
        $query = $this->createQueryBuilder(Task::ALIAS)
                    ->setParameter('userId', $user->getId());

        if ($this->security->isGranted('ROLE_ADMIN')) {
            $query
                ->where(Task::ALIAS.'.user = :userId')
                ->orWhere(Task::ALIAS.'.user is null');
        } else {
            $query
                ->where(Task::ALIAS.'.user = :userId');
        }

        if ($isExpired) {
            $query
                ->andWhere(Task::ALIAS.'.expiredAt < :date')
                ->setParameter('date', new \DateTime());
        } else {
            $query
                ->andWhere(Task::ALIAS.'.expiredAt > :date or '.Task::ALIAS.'.expiredAt is null')
                ->setParameter('date', new \DateTime());
        }

        $query
            ->andWhere(Task::ALIAS.'.isDone = :isDone')
            ->setParameter('isDone', $isDone);

        return $query
            ->getQuery()
            ->getResult();
    }
}