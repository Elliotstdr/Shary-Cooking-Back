<?php

namespace App\Service;

use App\Entity\Ingredient;
use App\Entity\IngredientData;
use App\Repository\IngredientDataRepository;
use App\Repository\IngredientTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CreateIngredientService extends AbstractController
{
  public function __construct(
    private readonly EntityManagerInterface $em,
    private readonly IngredientDataRepository $idr,
    private readonly IngredientTypeRepository $itr,
  ) {
  }

  public function createIngredient(Ingredient $ingredientItem, $newRecipe)
  {
    $ingredientLabel = ucfirst(strtolower($ingredientItem->getLabel()));
    $ingredientItem->setLabel(($ingredientLabel));
    $ingredientItem->setRecipe($newRecipe);

    $searchedIngredient = $this->idr->findOneBy(["name" => $ingredientLabel]);
    if (!$searchedIngredient) {
      $newIng = new IngredientData();
      $newIng->setName($ingredientLabel);
      $newIng->setType($this->itr->findOneBy(["label" => "unknown"]));
      $newIng->setFrequency(1);
      $this->em->persist($newIng);
    } else {
      $frequency = $searchedIngredient->getFrequency();
      if ($frequency) {
        $frequency += 1;
      } else {
        $frequency = 1;
      }
      $searchedIngredient->setFrequency($frequency);
      $this->em->persist($searchedIngredient);
    }

    $this->em->persist($ingredientItem);
  }
}
