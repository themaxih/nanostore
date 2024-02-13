<?php

namespace App\Repository;

use App\Entity\Produit;
use App\Entity\SousCategorie;
use App\Service\Sort\SortBy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 *
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    /**
     * Récupère une liste de produits dont les titres contiennent
     * au moins l'un des mots spécifiés dans le tableau donné en paramètre.
     * @param string[] $listeMots
     * @param SortBy|null $sortBy
     * @return Produit[]
     */
    public function findByTitleContaining(array $listeMots, ?SortBy $sortBy = null): array
    {
        $qb = $this->createQueryBuilder('p');
        foreach ($listeMots as $key => $mot) {
            $qb
                ->orWhere("MATCH(p.title, p.description) AGAINST(:mot".$key." BOOLEAN) > 0")
                ->setParameter('mot'.$key, '*'.$mot.'*');
            $qb
                ->orWhere("p.title LIKE :mot".$key)
                ->setParameter('mot'.$key, '%'.$mot.'%');
        }

        if (!is_null($sortBy)) {
            $qb->orderBy("p.$sortBy->column", $sortBy->order);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Récupère tout les produits des sous catégorie passé en paramètre.
     * @param SousCategorie[] $sousCategories
     * @return Produit[]
     */
    public function findAllBySubCategories(array $sousCategories, SortBy $sortBy): array
    {
        $sousCategorieNames = array_map(fn($sousCategorie) => $sousCategorie->getName(), $sousCategories);

        $qb = $this->createQueryBuilder('p')
            ->where('p.category IN (:sousCategories)')
            ->setParameter('sousCategories', $sousCategorieNames)
            ->orderBy("p.$sortBy->column", $sortBy->order);

        return $qb->getQuery()->getResult();
    }
}
