<?php

namespace App\Entity;

use App\Repository\AdresseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdresseRepository::class)]
#[ORM\UniqueConstraint('unique_address', ['user_id', 'gender', 'first_name', 'last_name', 'phone_number', 'address_line', 'postal_code', 'city'])]
class Adresse
{
    /** @noinspection PhpPropertyOnlyWrittenInspection */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'addresses')]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "user_id", onDelete: "CASCADE")]
    private Utilisateur $user;

    /**
     * false is a man, true a woman
     * @var bool
     */
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

    public function __construct(
        Utilisateur $user,
        bool $gender,
        string $firstName,
        string $lastName,
        string $phoneNumber,
        string $addressLine,
        string $postalCode,
        string $city
    ) {
        $this->user = $user;
        $this->gender = $gender;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->phoneNumber = $phoneNumber;
        $this->addressLine = $addressLine;
        $this->postalCode = $postalCode;
        $this->city = $city;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getGender(): bool {
        return $this->gender;
    }

    public function setGender(bool $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getAddressLine(): string
    {
        return $this->addressLine;
    }

    public function setAddressLine(string $addressLine): static
    {
        $this->addressLine = $addressLine;

        return $this;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getUser(): Utilisateur
    {
        return $this->user;
    }

    public function setUser(Utilisateur $user): void
    {
        $this->user = $user;
    }
}
