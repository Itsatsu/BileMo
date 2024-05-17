<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Hateoas\Configuration\Annotation as Hateoas;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "app_user_detail",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute= true
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups={"getUserDetail"}, excludeIf="expr(not is_granted('ROLE_USER'))")
 * )
 *
 * @Hateoas\Relation(
 *     "delete",
 *     href = @Hateoas\Route(
 *     "user_delete",
 *     parameters = { "id" = "expr(object.getId())" },
 *     absolute= true
 *     ),
 *     exclusion = @Hateoas\Exclusion(groups={"getUserDetail"}, excludeIf="expr(not is_granted('ROLE_USER'))")
 * )
 *
 * @Hateoas\Relation(
 *     "update",
 *     href = @Hateoas\Route(
 *     "user_update",
 *     parameters = { "id" = "expr(object.getId())" },
 *     absolute= true
 *     ),
 *     exclusion = @Hateoas\Exclusion(groups={"getUserDetail"}, excludeIf="expr(not is_granted('ROLE_USER'))")
 * )
 *
 *
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['getUserDetail'])]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    #[Groups(['getUserDetail'])]
    #[Assert\NotBlank(message: "L'email ne peut pas être vide")]
    #[Assert\Email(message: "L'email n'est pas valide")]
    #[Assert\Length(min: 5, max: 150, minMessage: "L'email doit contenir au moins 5 caractères", maxMessage: "L'email doit contenir au maximum 150 caractères")]
    private ?string $email = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['getUserDetail'])]
    private ?string $firstname = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['getUserDetail'])]
    private ?string $lastname = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Customer $customer = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): static
    {
        $this->customer = $customer;

        return $this;
    }
}
