<?php

namespace App\Repository;

use App\Entity\Mark;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class MarkRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Mark::class);
    }

    public function findAllMarkByMuseum($museumId)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT m FROM App\Entity\Mark m WHERE m.museum = :id');
        $query->setParameter('id', $museumId);
        $marks = $query->getResult();

        return $marks;
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('m')
            ->where('m.something = :value')->setParameter('value', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
