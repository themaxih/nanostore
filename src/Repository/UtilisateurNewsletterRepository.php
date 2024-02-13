<?php

namespace App\Repository;

use App\Entity\UtilisateurNewsletter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UtilisateurNewsletter>
 *
 * @method UtilisateurNewsletter|null find($id, $lockMode = null, $lockVersion = null)
 * @method UtilisateurNewsletter|null findOneBy(array $criteria, array $orderBy = null)
 * @method UtilisateurNewsletter[]    findAll()
 * @method UtilisateurNewsletter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UtilisateurNewsletterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UtilisateurNewsletter::class);
    }

//    /**
//     * @return UtilisateurNewsletter[] Returns an array of UtilisateurNewsletter objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UtilisateurNewsletter
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
