<?php

namespace App\Entity;

use App\Repository\BooksReservationsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BooksReservationsRepository::class)
 */
class BooksReservations
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="booksReservations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity=Books::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $books;

    /**
     * @ORM\Column(type="datetime")
     */
    private $reservedAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isCollected = false;


    public function __construct()
    {
        $this->reservedAt = new \DateTime();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getBooks(): ?Books
    {
        return $this->books;
    }

    public function setBooks(Books $books): self
    {
        $this->books = $books;

        return $this;
    }

    public function getReservedAt(): ?\DateTimeInterface
    {
        return $this->reservedAt;
    }

    public function setReservedAt(\DateTimeInterface $reservedAt): self
    {
        $this->reservedAt = $reservedAt;

        return $this;
    }

    public function getIsCollected(): ?bool
    {
        return $this->isCollected;
    }

    public function setIsCollected(bool $isCollected): self
    {
        $this->isCollected = $isCollected;

        return $this;
    }
}
