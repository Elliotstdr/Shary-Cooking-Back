<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\StepRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource]
#[ORM\Entity(repositoryClass: StepRepository::class)]
class Step
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['step:read', 'recipe:item:read', 'recipe:put'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Recipe::class, inversedBy: 'steps')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['step:read'])]
    private ?Recipe $recipe = null;

    #[ORM\Column(length: 10000)]
    #[Groups(['step:read', 'recipe:item:read', 'recipe:put', 'recipe:read'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['step:read', 'recipe:item:read', 'recipe:put', 'recipe:read'])]
    private ?int $stepIndex = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(?Recipe $recipe): self
    {
        $this->recipe = $recipe;

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

    public function getStepIndex(): ?int
    {
        return $this->stepIndex;
    }

    public function setStepIndex(int $stepIndex): self
    {
        $this->stepIndex = $stepIndex;

        return $this;
    }
}
