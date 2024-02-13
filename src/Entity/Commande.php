<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    /** @noinspection PhpPropertyOnlyWrittenInspection */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "order_id")]
    private int $id;

    #[ORM\Column]
    private float $totalPrice = 0;

    #[ORM\Column]
    private bool $gender;

    #[ORM\Column(length: 32)]
    private string $firstName;

    #[ORM\Column(length: 32)]
    private string $lastName;

    #[ORM\Column(length: 10)]
    private string $phoneNumber;

    #[ORM\Column(length: 255)]
    private string $addressLine;

    #[ORM\Column(length: 5)]
    private string $postalCode;

    #[ORM\Column(length: 50)]
    private string $city;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'orders')]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "user_id", onDelete: "CASCADE")]
    private Utilisateur $user;

    #[ORM\OneToMany(mappedBy: 'order', targetEntity: CommandeProduit::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $orderProducts;

    #[ORM\Column(type: 'date')]
    private DateTimeInterface $orderDate;

    function __construct()
    {
        $this->orderProducts = new ArrayCollection();
        $this->orderDate = new DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): Utilisateur
    {
        return $this->user;
    }

    public function setUser(Utilisateur $user): void
    {
        $this->user = $user;
    }

    public function getOrderProducts(): Collection
    {
        return $this->orderProducts;
    }

    public function getTotalPrice(): float
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(float $totalPrice): void
    {
        $this->totalPrice = $totalPrice;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function getAddressLine(): string
    {
        return $this->addressLine;
    }

    public function setAddressLine(string $addressLine): void
    {
        $this->addressLine = $addressLine;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): void
    {
        $this->postalCode = $postalCode;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getOrderDate(): DateTime
    {
        return $this->orderDate;
    }

    public function addOrderProducts(CommandeProduit $orderProduct): void
    {
        $this->orderProducts->add($orderProduct);
    }

    public function isGender(): bool
    {
        return $this->gender;
    }

    public function setGender(bool $gender): void
    {
        $this->gender = $gender;
    }
}
