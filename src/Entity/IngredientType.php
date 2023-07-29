<?php

namespace App\Entity;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\IngredientTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [new Get(), new GetCollection(normalizationContext: ['groups' => ['ingtype:read']])]
)]
#[ORM\Entity(repositoryClass: IngredientTypeRepository::class)]
class IngredientType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['ingtype:read', 'ingdata:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['ingtype:read', 'ingdata:read'])]
    private ?string $label = null;

    #[ORM\OneToMany(mappedBy: 'type', targetEntity: IngredientData::class)]
    private Collection $datas;

    public function __construct()
    {
        $this->datas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return Collection<int, IngredientData>
     */
    public function getDatas(): Collection
    {
        return $this->datas;
    }

    public function addData(IngredientData $data): self
    {
        if (!$this->datas->contains($data)) {
            $this->datas->add($data);
            $data->setType($this);
        }

        return $this;
    }

    public function removeData(IngredientData $data): self
    {
        if ($this->datas->removeElement($data)) {
            // set the owning side to null (unless already changed)
            if ($data->getType() === $this) {
                $data->setType(null);
            }
        }

        return $this;
    }
}
