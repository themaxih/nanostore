<?php

namespace App\Service;

use App\Entity\Commande;
use App\Entity\CommandeProduit;
use App\Entity\PanierProduit;
use App\Entity\Produit;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;

class OrderBuilder
{
    private Commande $commande;
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->commande = new Commande();
    }


    public function withShippingDetails(
        bool $gender,
        string $firstName,
        string $lastName,
        string $phoneNumber,
        string $address,
        string $postalCode,
        string $city)
    : self
    {
        $this->commande->setGender($gender);
        $this->commande->setFirstName($firstName);
        $this->commande->setLastName($lastName);
        $this->commande->setPhoneNumber($phoneNumber);
        $this->commande->setAddressLine($address);
        $this->commande->setPostalCode($postalCode);
        $this->commande->setCity($city);
        return $this;
    }

    public function createOrderFor(Utilisateur $user): self
    {
        $this->commande->setUser($user);

        /** @var PanierProduit $panierProduit */
        foreach ($user->getCartProducts() as $panierProduit) {
            $produit = $panierProduit->getProduct();
            if ($produit->getQuantityLeft() != 0) {
                $quantityChosen = $panierProduit->getQuantityChosen();
                $this->addProduct(
                    $panierProduit->getProduct(),
                    min($quantityChosen, $produit->getQuantityLeft())
                );
                $produit->substractQuantityFromRemaining($quantityChosen);
                $this->entityManager->remove($panierProduit);
            }
        }

        return $this;
    }

    private function addProduct(Produit $produit, int $quantity): void
    {
        $prixTotal = $this->commande->getTotalPrice();
        $prixTotal += $produit->getPrice() * $quantity;
        $this->commande->setTotalPrice($prixTotal);
        $commandeProduit = new CommandeProduit($this->commande, $produit, $quantity);
        $this->commande->addOrderProducts($commandeProduit);
    }

    public function validateOrder(): void
    {
        $this->entityManager->persist($this->commande);
        $this->entityManager->flush();
    }
}