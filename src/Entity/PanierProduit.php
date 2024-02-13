<?php

namespace App\Entity;

use App\Repository\PanierProduitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PanierProduitRepository::class)]
class PanierProduit
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'cartProducts')]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "user_id", onDelete: "CASCADE")]
    private Utilisateur $user;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Produit::class)]
    #[ORM\JoinColumn(name: "product_id", referencedColumnName: "id")]
    private Produit $product;

    #[ORM\Column]
    private int $quantityChosen;

    function __construct(Utilisateur $user, Produit $product, int $quantityChosen)
    {
        $this->user = $user;
        $this->product = $product;
        $this->quantityChosen = $quantityChosen;
    }

    public function getProduct(): Produit
    {
        return $this->product;
    }

    public function getQuantityChosen(): int
    {
        return $this->quantityChosen;
    }

    public function setQuantityChosen(int $quantityChosen): void
    {
        $this->quantityChosen = $quantityChosen;
    }

    public function getUser(): Utilisateur
    {
        return $this->user;
    }
}
