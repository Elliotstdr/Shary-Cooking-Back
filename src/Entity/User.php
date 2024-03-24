<?php

namespace App\Entity;

use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use App\Controller\User\CreateAccount;
use App\Controller\User\LoginCheck;
use App\Controller\User\MailController;
use App\Controller\User\PutUser;
use App\Controller\User\ResetPassword;
use App\Controller\User\SendResetMail;
use App\Controller\User\UserByEmail;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\HttpFoundation\File\File;

#[ApiResource(
    operations: [
        new Get(security: "is_granted('OWN', id)"),
        new Post(
            controller: CreateAccount::class,
            uriTemplate: 'users/createAccount',
            inputFormats: ['json' => ['application/json']],
            denormalizationContext: ['groups' => ['user:write']]
        ),
        new Put(
            controller: PutUser::class,
            inputFormats: ['json' => ['application/json']],
            denormalizationContext: ['groups' => ['user:put']],
            read: false,
            security: "is_granted('OWN', id)"
        ),
        new Post(uriTemplate: 'users/by_email', controller: UserByEmail::class, read: false),
        new Post(uriTemplate: 'users/loginCheck', controller: LoginCheck::class),
        new Post(uriTemplate: 'users/mailReset', controller: SendResetMail::class),
        new Post(uriTemplate: 'users/resetPassword', controller: ResetPassword::class),
        new Post(
            uriTemplate: 'users/sendReport',
            controller: MailController::class,
            inputFormats: ['json' => ['application/json']],
            read: false
        )
    ],
    normalizationContext: ['groups' => ['user:read']]
)]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiFilter(filterClass: SearchFilter::class, properties: ['email' => 'exact'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read', 'user:write', 'recipe:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'user:write', 'user:put', 'recipe:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'user:write', 'user:put'])]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'user:write', 'user:put'])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:write'])]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read', 'recipe:read'])]
    private ?string $imageUrl = null;

    private ?File $image = null;

    #[ORM\ManyToMany(targetEntity: Recipe::class, mappedBy: 'savedByUsers')]
    private Collection $savedRecipes;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $resetPassword = null;

    public function __construct()
    {
        $this->savedRecipes = new ArrayCollection();
    }

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

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function getImage(): ?File
    {
        return $this->image;
    }

    public function setImage(File $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, Recipe>
     */
    public function getSavedRecipes(): Collection
    {
        return $this->savedRecipes;
    }

    public function addSavedRecipe(Recipe $savedRecipe): self
    {
        if (!$savedRecipe) {
            return false;
        }
        if (!$this->savedRecipes->contains($savedRecipe)) {
            $this->savedRecipes->add($savedRecipe);
            $savedRecipe->addSavedByUser($this);
        }

        return $this;
    }

    public function removeSavedRecipe(Recipe $savedRecipe): self
    {
        if ($this->savedRecipes->removeElement($savedRecipe)) {
            $savedRecipe->removeSavedByUser($this);
        }

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        return array('ROLE_USER');
    }

    public function eraseCredentials()
    {
    }

    public function getResetPassword(): ?string
    {
        return $this->resetPassword;
    }

    public function setResetPassword(?string $resetPassword): self
    {
        $this->resetPassword = $resetPassword;

        return $this;
    }
}
