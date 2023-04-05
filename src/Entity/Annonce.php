<?php

namespace App\Entity;

use App\Repository\AnnonceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Cocur\Slugify\Slugify;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;




#[ORM\Entity(repositoryClass: AnnonceRepository::class), ORM\HasLifecycleCallbacks]
#[UniqueEntity('slug')]
class Annonce
{
    const STATUS_VERY_BAD = 0;
    const STATUS_BAD = 1;
    const STATUS_GOOD = 2;
    const STATUS_VERY_GOOD = 3;
    const STATUS_PERFECT = 4;



    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;



    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(
        min: 40,
        minMessage: "La description doit faire plus de {{ limit }} caractères",
    )]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\Column]
    private ?int $status = null;

    #[ORM\Column(options: ['default' => false])]
    private ?bool $isSold = false;

    #[ORM\Column]
    private ?\DateTimeImmutable $createAt = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(options: [
        'default' => 'CURRENT_TIMESTAMP' // c'est pour mettre une valeur par défaut, sinon la migration ne fonctionnera pas
    ])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[Assert\Url(
        protocols: ['https'],

    )]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\PrePersist]
    public function prePersist()
    {
        $this->createAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->slug = (new Slugify())->slugify($this->title);
    }

    #[ORM\PreUpdate]
    public function preUpdate()
    {
        $this->updatedAt = new \DateTimeImmutable();
        $this->slug = (new Slugify())->slugify($this->title);
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

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        $allowedStatus = [
            self::STATUS_VERY_BAD,
            self::STATUS_BAD,
            self::STATUS_GOOD,
            self::STATUS_VERY_GOOD,
            self::STATUS_PERFECT
        ];

        if (!in_array($status, $allowedStatus)) {
            throw new \InvalidArgumentException('Invalid status');
        }

        $this->status = $status;

        return $this;
    }

    public function isIsSold(): ?bool
    {
        return $this->isSold;
    }

    public function setIsSold(bool $isSold): self
    {
        $this->isSold = $isSold;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeImmutable
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeImmutable $createAt): self
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getSlug(): ?string
    {
        if (!$this->slug) {
            $this->setSlug($this->title);
        }
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $slugify = new Slugify();
        $this->slug = $slugify->slugify($slug);

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }
}
