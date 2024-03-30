<?php

namespace App\Entity;

use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\ApiResource;
use App\Controller\Recipe\CreateRecipe;
use App\Controller\Recipe\DeletePicture;
use App\Controller\Recipe\FindHelloRecipes;
use App\Controller\Recipe\PutRecipe;
use App\Controller\Recipe\SaveRecipe;
use App\Repository\RecipeRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            controller: CreateRecipe::class,
            inputFormats: ['json' => ['application/json']],
            security: "is_granted('NOT_GUEST', 0)",
            securityMessage: "Vous ne pouvez pas créer de recette avec un compte visiteur"
        ),
        new Put(
            controller: PutRecipe::class,
            inputFormats: ['json' => ['application/json']],
            denormalizationContext: ['groups' => ['recipe:put']],
            security: "is_granted('NOT_GUEST', id)",
            securityMessage: "Vous ne pouvez pas modifier cette recette avec un compte visiteur"
        ),
        new Delete(),
        new Post(
            uriTemplate: 'recipes/hellof',
            controller: FindHelloRecipes::class,
            read: false,
        ),
        new Post(
            uriTemplate: 'recipes/{recipeId}/users/{userId}',
            controller: SaveRecipe::class,
            read: false,
            security: "is_granted('OWN', userId)"
        ),
        new Post(
            uriTemplate: 'recipes/{id}/deletePicture',
            controller: DeletePicture::class,
            security: "is_granted('NOT_GUEST', id)",
            securityMessage: "Vous ne pouvez pas modifier cette recette avec un compte visiteur"
        ),
    ],
    denormalizationContext: ['groups' => ['recipe:write']],
    normalizationContext: ['groups' => ['recipe:read']]
)]
#[ORM\Entity(repositoryClass: RecipeRepository::class)]
class Recipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['recipe:read', 'recipe:write', 'recipe:put'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['recipe:read', 'recipe:write', 'recipe:put'])]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['recipe:read', 'recipe:write', 'recipe:put'])]
    private ?string $time = null;

    #[ORM\Column]
    #[Groups(['recipe:read', 'recipe:write', 'recipe:put'])]
    private ?int $number = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['recipe:read', 'recipe:write', 'recipe:put'])]
    private ?Type $type = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['recipe:read', 'recipe:write', 'recipe:put'])]
    private ?Regime $regime = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'savedRecipes', cascade: ['persist'])]
    #[Groups(['recipe:read'])]
    private Collection $savedByUsers;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['recipe:read', 'recipe:write', 'recipe:put'])]
    private ?User $postedByUser = null;

    #[ORM\OneToMany(targetEntity: Ingredient::class, mappedBy: 'recipe', cascade: ['persist'], orphanRemoval: true)]
    #[Groups(['recipe:put', 'recipe:read'])]
    private $ingredients;

    #[ORM\OneToMany(targetEntity: Step::class, mappedBy: 'recipe', cascade: ['persist'], orphanRemoval: true)]
    #[Groups(['recipe:put', 'recipe:read'])]
    private Collection $steps;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['recipe:read'])]
    private ?string $imageUrl = null;

    private ?File $image = null;

    #[ORM\Column(nullable: false)]
    #[Groups(['recipe:read', 'recipe:write', 'recipe:put'])]
    private ?DateTimeImmutable $createdAt = null;

    #[Groups(['recipe:read', 'recipe:write', 'recipe:put'])]
    #[ORM\Column(nullable: true)]
    private ?bool $fromHellof = null;

    public function __construct()
    {
        $this->savedByUsers = new ArrayCollection();
        $this->ingredients = new ArrayCollection();
        $this->steps = new ArrayCollection();
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

    public function getTime(): ?string
    {
        return $this->time;
    }

    public function setTime(string $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getRegime(): ?Regime
    {
        return $this->regime;
    }

    public function setRegime(?Regime $regime): self
    {
        $this->regime = $regime;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getSavedByUsers(): Collection
    {
        return $this->savedByUsers;
    }

    public function addSavedByUser(User $savedByUser): self
    {
        if (!$this->savedByUsers->contains($savedByUser)) {
            $this->savedByUsers->add($savedByUser);
        }

        return $this;
    }

    public function removeSavedByUser(User $savedByUser): self
    {
        $this->savedByUsers->removeElement($savedByUser);

        return $this;
    }

    public function getPostedByUser(): ?User
    {
        return $this->postedByUser;
    }

    public function setPostedByUser(?User $postedByUser): self
    {
        $this->postedByUser = $postedByUser;

        return $this;
    }

    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

    public function addIngredients(Ingredient $ingredients): self
    {
        if (!$this->ingredients->contains($ingredients)) {
            $this->ingredients->add($ingredients);
        }

        return $this;
    }

    public function removeIngredients(Ingredient $ingredients): self
    {
        $this->ingredients->removeElement($ingredients);

        return $this;
    }

    public function removeAllIngredients(): static
    {
        $this->ingredients->clear();

        return $this;
    }

    public function getSteps(): Collection
    {
        return $this->steps;
    }

    public function addSteps(Step $steps): self
    {
        if (!$this->steps->contains($steps)) {
            $this->steps->add($steps);
        }

        return $this;
    }

    public function removeSteps(Step $steps): self
    {
        $this->steps->removeElement($steps);

        return $this;
    }

    public function removeAllSteps(): static
    {
        $this->steps->clear();

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

    public function getImage(): ?File
    {
        return $this->image;
    }

    public function setImage(?File $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function isFromHellof(): ?bool
    {
        return $this->fromHellof;
    }

    public function setFromHellof(?bool $fromHellof): static
    {
        $this->fromHellof = $fromHellof;

        return $this;
    }
}
