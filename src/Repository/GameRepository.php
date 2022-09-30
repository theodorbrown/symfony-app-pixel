<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

class GameRepository extends ServiceEntityRepository {
    
    public function __construct(ManagerRegistry $registry)
    {
        //indique que le repository est associé à l'entité Game
        parent:: __construct($registry, Game:: class);
    }

    public function findAll(): array {
        $qb = $this->createQueryBuilder('g');
        $this->addJoin($qb);
        
        return $qb->getQuery()->getResult();
    }

    public function findEnabled(): array {
        $qb = $this->createQueryBuilder('g');
        $this->addJoin($qb);
        $qb->where('g.enabled = true');
        
        return $qb->getQuery()->getResult();
    }

    public function findByUser(User $user): array {
        $qb = $this->createQueryBuilder('g');
        $this->addJoin($qb);
        $qb->where('g.user = :user')
            ->setParameter(':user', $user);
        
        return $qb->getQuery()->getResult();
    }

    public function findPagination(int $page = 1, int $itemCount = 20): Paginator {
        $begin = ($page - 1) * $itemCount;

        $qb = $this->createQueryBuilder('g')
            ->setMaxResults($itemCount)
            ->setFirstResult($begin);

        $this->addJoin($qb);

        return new Paginator($qb->getQuery());
    }

    private function addJoin(QueryBuilder $qb): void {
        $qb ->addSelect('i, s, u, c')
            ->leftJoin('g.image','i')
            ->leftJoin('g.support','s')
            ->leftJoin('g.user','u')
            ->leftJoin('g.categories', 'c');
    }
}