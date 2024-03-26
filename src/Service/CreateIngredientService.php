<?php

namespace App\Service;

use App\Entity\Ingredient;
use App\Entity\IngredientData;
use App\Entity\Recipe;
use App\Repository\IngredientDataRepository;
use App\Repository\IngredientTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CreateIngredientService extends AbstractController
{
  public function __construct(
    private readonly EntityManagerInterface $em,
    private readonly IngredientDataRepository $ingredientDataRepository,
    private readonly IngredientTypeRepository $ingredientTypeRepository,
  ) {
  }

  public function createIngredient(Ingredient $ingredientItem, Recipe $newRecipe)
  {
    $ingredientLabel = ucfirst(strtolower($ingredientItem->getLabel()));
    $ingredientItem->setLabel(($ingredientLabel));
    $ingredientItem->setRecipe($newRecipe);

    $searchedIngredient = $this->ingredientDataRepository->findOneBy(["name" => $ingredientLabel]);
    if (!$searchedIngredient) {
      $newIng = new IngredientData();
      $newIng->setName($ingredientLabel);
      $newIng->setType($this->ingredientTypeRepository->findOneBy(["label" => "unknown"]));
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
