<?php

namespace App\Entity;

use App\Repository\UtilisateurNewsletterRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UtilisateurNewsletterRepository::class)]
class UtilisateurNewsletter
{
    #[ORM\Id]
    #[ORM\Column]
    private string $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public function getEMail(): string
    {
        return $this->email;
    }
}
