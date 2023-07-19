<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\User\CreateAccount;
use App\Controller\User\LoginCheck;
use App\Controller\User\MailController;
use App\Controller\User\PutUser;
use App\Controller\User\ResetPassword;
use App\Controller\User\SendResetMail;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiFilter(SearchFilter::class, properties: [
    'email' => 'exact'
])]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['user:read']]
        ],
        'create_account' => [
            'method' => 'POST',
            'path' => 'users/createAccount',
            'controller' => CreateAccount::class,
            'input_formats' => ['json' => ['application/json']],
            'denormalization_context' => ['groups' => ['user:write']]
        ],
        'login_check' => [
            'method' => 'POST',
            'path' => 'users/loginCheck',
            'controller' => LoginCheck::class,
            'input_formats' => ['json' => ['application/json']],
            'normalization_context' => ['groups' => ['user:read', 'recipe:read', 'type:read', 'regime:read']],
            'denormalization_context' => ['groups' => ['user:login']]
        ],
        'mail_reset' => [
            'method' => 'POST',
            'path' => 'users/mailReset',
            'controller' => SendResetMail::class,
            'input_formats' => ['json' => ['application/json']],
            'normalization_context' => ['groups' => ['user:reset']],
            'denormalization_context' => ['groups' => ['user:reset']]
        ],
        'reset_password' => [
            'method' => 'POST',
            'path' => 'users/resetPassword',
            'controller' => ResetPassword::class,
            'input_formats' => ['json' => ['application/json']],
            'normalization_context' => ['groups' => ['user:reset']],
            'denormalization_context' => ['groups' => ['user:reset']]
        ],
        'send_report' => [
            'method' => 'POST',
            'path' => 'users/sendReport',
            'controller' => MailController::class,
            'input_formats' => ['json' => ['application/json']],
            'read' => false,
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['user:read', 'recipe:read', 'type:read', 'regime:read']]
        ],
        'put' => [
            'controller' => PutUser::class,
            'input_formats' => ['json' => ['application/json']],
            'denormalization_context' => ['groups' => ['user:put']],
            'read' => false
        ],
        'delete',
    ],
    normalizationContext: ['groups' => ['user:read']]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read', 'user:write', 'recipe:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'user:write', 'recipe:read', 'user:put'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'user:write', 'recipe:read', 'user:put'])]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'user:write', 'user:login', 'recipe:read', 'user:put', 'user:reset'])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:write', 'user:login'])] //'user:read', 'recipe:read'
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read', 'recipe:read'])]
    private ?string $imageUrl = null;

    #[Groups(['user:image:upload'])]
    private ?File $image = null;

    #[ORM\ManyToMany(targetEntity: Recipe::class, mappedBy: 'savedByUsers')]
    private Collection $savedRecipes;

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
}
