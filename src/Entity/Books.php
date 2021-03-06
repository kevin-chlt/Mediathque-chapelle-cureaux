<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\BooksRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ApiResource(collectionOperations={"GET"}, itemOperations={"GET"}, formats={"json"})
 * @ORM\Entity(repositoryClass=BooksRepository::class)
 * @UniqueEntity(fields={"title"}, message="Un livre existe déjà avec ce titre.")
 */
class Books
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le champ 'Titre' doit être renseigné")
     * @Assert\Length(min=3, minMessage="Le champ 'Titre' doit contenir au minimum {{ limit }} caractères.",
     *     max=255, maxMessage="Le champ 'Titre' doit contenir au maximum {{ limit }} caractères.")
     * @Assert\Regex(pattern="/^[.A-z0-9À-ÿ \/'-?!&;:,]+$/", message=" 'Titre': Veuillez utiliser seulement des lettres, des chiffres, ou les caractères suivants: /'-?!&,;: .")
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Le champ 'Description' doit être renseigné")
     * @Assert\Regex(pattern="/^[.A-z0-9À-ÿ \/'-?!&;:,]+$/", message=" 'Description': Veuillez utiliser seulement des lettres, des chiffres, ou les caractères suivants: /'-?!&,;: .")
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank(message="Le champ 'Parution' doit être renseigné")
     * @Assert\LessThan(value="+5 years", message="Date incorrecte.")
     */
    private $parutedAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isFree = true;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\File(mimeTypes={"image/jpeg","image/png","image/svg+xml"}, mimeTypesMessage="Type d'images acceptées: PNG, JPG, SVG",
     *     maxSize="2048k", maxSizeMessage="Taille de fichier maximum autorisé: {{ limit }}")
     */
    private $cover;

    /**
     * @ORM\ManyToMany(targetEntity=Categories::class, inversedBy="books", cascade={"persist"})
     * @Assert\NotBlank(message="Le champ 'Categories' doit être renseigné")
     */
    private $categories;

    /**
     * @ORM\ManyToMany(targetEntity=Authors::class, inversedBy="books", cascade={"persist"})
     * @Assert\NotBlank(message="Le champ 'Auteurs' doit être renseigné")
     */
    private $authors;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->authors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getParutedAt(): ?\DateTimeInterface
    {
        return $this->parutedAt;
    }

    public function setParutedAt(\DateTimeInterface $parutedAt): self
    {
        $this->parutedAt = $parutedAt;

        return $this;
    }

    public function getIsFree(): ?bool
    {
        return $this->isFree;
    }

    public function setIsFree(bool $isFree): self
    {
        $this->isFree = $isFree;

        return $this;
    }

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(string $cover): self
    {
        $this->cover = $cover;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Categories $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Categories $category): self
    {
        $this->categories->removeElement($category);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    public function addAuthor(Authors $author): self
    {
        if (!$this->authors->contains($author)) {
            $this->authors[] = $author;
        }

        return $this;
    }

    public function removeAuthor(Authors $author): self
    {
        $this->authors->removeElement($author);

        return $this;
    }
}
