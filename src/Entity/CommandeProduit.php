<?php

namespace App\Entity;

use App\Repository\CommandeProduitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeProduitRepository::class)]
class CommandeProduit
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Commande::class, inversedBy: 'orderProducts')]
    #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'order_id', nullable: false, onDelete: "CASCADE")]
    private Commande $order;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Produit::class)]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', nullable: false)]
    private Produit $product;

    #[ORM\Column(type: 'integer')]
    private int $quantity;

    function __construct(Commande $order, Produit $product, int $quantityChosen)
    {
        $this->order = $order;
        $this->product = $product;
        $this->quantity = $quantityChosen;
    }

    public function getOrder(): Commande
    {
        return $this->order;
    }

    public function getProduct(): Produit
    {
        return $this->product;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
