<?php /** @noinspection PhpPropertyOnlyWrittenInspection */

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
#[ORM\Cache(usage: "READ_ONLY", region: "categories_regions")]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(length: 255)]
    private string $hrefName;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: SousCategorie::class, orphanRemoval: true)]
    private Collection $subCategories;

    public function __construct() {
        $this->subCategories = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSubCategories(): Collection
    {
        return $this->subCategories;
    }

    public function getHrefName(): string
    {
        return $this->hrefName;
    }
}
