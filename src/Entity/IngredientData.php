<?php

namespace App\Entity;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\IngredientDataRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [new Get(), new GetCollection()],
    normalizationContext: ['groups' => ['ingdata:read']]
)]
#[ORM\Entity(repositoryClass: IngredientDataRepository::class)]
class IngredientData
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['ingdata:read'])]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'datas')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['ingdata:read'])]
    private ?IngredientType $type = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['ingdata:read'])]
    private ?int $frequency = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?IngredientType
    {
        return $this->type;
    }

    public function setType(?IngredientType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getFrequency(): ?int
    {
        return $this->frequency;
    }

    public function setFrequency(?int $frequency): static
    {
        $this->frequency = $frequency;

        return $this;
    }
}
