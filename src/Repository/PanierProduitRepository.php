<?php

namespace App\Repository;

use App\Entity\PanierProduit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PanierProduit>
 *
 * @method PanierProduit|null find($id, $lockMode = null, $lockVersion = null)
 * @method PanierProduit|null findOneBy(array $criteria, array $orderBy = null)
 * @method PanierProduit[]    findAll()
 * @method PanierProduit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PanierProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PanierProduit::class);
    }

    /**
     * @param int $user_id
     * @return PanierProduit[]
     */
    public function findCartProductsByUserId(int $user_id): array
    {
        return $this->createQueryBuilder('pp')
            ->innerJoin('pp.product', 'p')
            ->addSelect('p')
            ->where('pp.user = :user_id')
            ->setParameter('user_id', $user_id)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $user_id
     * @return float
     */
    public function getTotalCartPrice(int $user_id): float
    {
        try {
            return $this->createQueryBuilder('pp')
                ->select('SUM(pp.quantityChosen * p.price) AS total_price')
                ->innerJoin('pp.product', 'p')
                ->where('pp.user = :user_id')
                ->setParameter('user_id', $user_id)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException|NonUniqueResultException) {
            return 0.0;
        }
    }

}
