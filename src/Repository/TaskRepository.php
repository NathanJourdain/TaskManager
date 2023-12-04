<?php

namespace App\Repository;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }


    
   /**
    * @return Task[] Returns an array of Task objects
    */
   public function findUserTasks(User $user, bool $onlyTodo = true): array
   {
        $query = $this->createQueryBuilder('t');

        // regarder si l'utilisateur est un employé
        if(in_array('ROLE_EMPLOYEE', $user->getRoles())) {
            $query->andWhere('t.assignedTo = :user OR t.assignedTo IS NULL')
                ->setParameter('user', $user)
                ->andWhere('t.enable = true');
        }
        
        if($onlyTodo){
            $query->andWhere('t.lastCompletedAt IS NULL OR :now >= DATE_ADD(t.lastCompletedAt, t.recurrence, \'day\')')
                ->setParameter('now', new \DateTime());
        }

        $query->orderBy('t.id', 'ASC');

        return $query->getQuery()
                ->getResult();
   }


//    /**
//     * @return Task[] Returns an array of Task objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Task
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
