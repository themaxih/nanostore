<?php /** @noinspection PhpPropertyOnlyWrittenInspection */

namespace App\Entity;

use App\Repository\SousCategorieRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SousCategorieRepository::class)]
#[ORM\Cache(usage: "READ_ONLY", region: "categories_regions")]
class SousCategorie
{
    #[ORM\Id]
    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\ManyToOne(targetEntity: Categorie::class, inversedBy: 'subCategories')]
    #[ORM\JoinColumn(nullable: false)]
    private Categorie $category;

    #[ORM\Column(length: 255)]
    private string $hrefName;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Produit::class)]
    private Collection $products;

    public function getName(): string
    {
        return $this->name;
    }

    public function getCategory(): Categorie
    {
        return $this->category;
    }

    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function getHrefName(): string
    {
        return $this->hrefName;
    }
}
