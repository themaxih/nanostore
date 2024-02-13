<?php /** @noinspection PhpPropertyOnlyWrittenInspection */

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
#[ORM\Index(columns: ['title', 'description'], name: 'title_fulltext', flags: ['fulltext'])]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(length: 255)]
    private string $description;

    #[ORM\Column]
    private int $quantityLeft;

    #[ORM\Column]
    private float $price;

    #[ORM\ManyToOne(targetEntity: SousCategorie::class, inversedBy: 'products')]
    #[ORM\JoinColumn(name: "sub_category", referencedColumnName: "name", nullable: false)]
    private SousCategorie $category;

    #[ORM\Column(length: 50)]
    private string $nomImage;

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getQuantityLeft(): int
    {
        return $this->quantityLeft;
    }

    public function substractQuantityFromRemaining(int $quantityChosen): void {
        $this->quantityLeft -= $quantityChosen;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getNomImage(): string
    {
        return $this->nomImage;
    }

    public function getCategory(): SousCategorie
    {
        return $this->category;
    }
}
