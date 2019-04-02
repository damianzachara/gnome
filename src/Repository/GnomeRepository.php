<?php

namespace App\Repository;

use App\Entity\Gnome;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Gnome|null find($id, $lockMode = null, $lockVersion = null)
 * @method Gnome|null findOneBy(array $criteria, array $orderBy = null)
 * @method Gnome[]    findAll()
 * @method Gnome[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GnomeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Gnome::class);
    }

    // /**
    //  * @return Gnome[] Returns an array of Gnome objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Gnome
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
