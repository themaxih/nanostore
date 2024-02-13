<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Un compte avec cette adresse mail existe déjà.')]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    /** @noinspection PhpPropertyOnlyWrittenInspection */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $user_id ;

    #[ORM\Column(length: 180, unique: true)]
    private string $email;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private string $password;

    #[ORM\Column(length: 32)]
    private string $firstName;

    #[ORM\Column(length: 32)]
    private string $lastName;

    /**
     * false is a man, true a woman
     * @var bool
     */
    #[ORM\Column]
    private bool $gender;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Adresse::class)]
    private Collection $addresses;

    #[ORM\OneToOne(targetEntity: Adresse::class)]
    #[ORM\JoinColumn(name: "default_address_id")]
    private ?Adresse $defaultAddress = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Commande::class)]
    private Collection $orders;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: PanierProduit::class)]
    private Collection $cartProducts;

    public function __construct() {
        $this->orders = new ArrayCollection();
        $this->cartProducts = new ArrayCollection();
        $this->addresses = new ArrayCollection();
    }

    public function getUserId(): int {
        return $this->user_id;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function setEmail(string $email): static {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string {
        return $this->firstName." ".$this->lastName;
    }

    public function getRoles(): array {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string {
        return $this->password;
    }

    public function setPassword(string $password): static {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials(): void {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getGender(): bool {
        return $this->gender;
    }

    public function setGender(bool $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function getOrders(): Collection {
        return $this->orders;
    }

    public function setOrders(Collection $orders): void
    {
        $this->orders = $orders;
    }

    public function getCartProducts(): Collection
    {
        return $this->cartProducts;
    }

    public function countCartProducts(): int {
        return count($this->cartProducts);
    }

    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    public function getDefaultAddress(): ?Adresse
    {
        return $this->defaultAddress;
    }

    public function setDefaultAddress(?Adresse $defaultAddress): void
    {
        $this->defaultAddress = $defaultAddress;
    }
}
