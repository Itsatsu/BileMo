<?php

namespace App\Entity;

use App\Repository\PhoneRepository;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation\Groups;

/**
 * @Hateoas\Relation(
 *     "self",
 *     href = @Hateoas\Route(
 *     "app_phone_detail",
 *     parameters = { "id" = "expr(object.getId())"}
 *     ),
 *     exclusion = @Hateoas\Exclusion(groups = {"getPhones"}, excludeIf = "expr(not is_granted('ROLE_USER'))"),
 * )
 */
#[ORM\Entity(repositoryClass: PhoneRepository::class)]
class Phone
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getPhones", "getPhoneDetail"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getPhones", "getPhoneDetail"])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(["getPhones", "getPhoneDetail"])]
    private ?float $price = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getPhoneDetail"])]
    private ?string $color = null;

    #[ORM\Column]
    #[Groups(["getPhoneDetail"])]
    private ?bool $fiveG = null;

    #[ORM\Column]
    #[Groups(["getPhoneDetail"])]
    private ?bool $fourG = null;

    #[ORM\Column]
    #[Groups(["getPhoneDetail"])]
    private ?int $battery = null;

    #[ORM\Column]
    #[Groups(["getPhoneDetail"])]
    private ?int $storage = null;

    #[ORM\Column]
    #[Groups(["getPhoneDetail"])]
    private ?float $screenDiagonal = null;

    #[ORM\ManyToOne(inversedBy: 'phones')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["getPhones", "getPhoneDetail"])]
    private ?Brand $brand = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function isFiveG(): ?bool
    {
        return $this->fiveG;
    }

    public function setFiveG(bool $fiveG): static
    {
        $this->fiveG = $fiveG;

        return $this;
    }

    public function isFourG(): ?bool
    {
        return $this->fourG;
    }

    public function setFourG(bool $fourG): static
    {
        $this->fourG = $fourG;

        return $this;
    }

    public function getBattery(): ?int
    {
        return $this->battery;
    }

    public function setBattery(int $battery): static
    {
        $this->battery = $battery;

        return $this;
    }

    public function getStorage(): ?int
    {
        return $this->storage;
    }

    public function setStorage(int $storage): static
    {
        $this->storage = $storage;

        return $this;
    }

    public function getScreenDiagonal(): ?float
    {
        return $this->screenDiagonal;
    }

    public function setScreenDiagonal(float $screenDiagonal): static
    {
        $this->screenDiagonal = $screenDiagonal;

        return $this;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): static
    {
        $this->brand = $brand;

        return $this;
    }
}
