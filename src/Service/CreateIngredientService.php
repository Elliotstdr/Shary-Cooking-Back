<?php

namespace App\Service;

use App\Entity\Ingredient;
use App\Entity\IngredientData;
use App\Repository\IngredientDataRepository;
use App\Repository\IngredientTypeRepository;
use App\Repository\UnitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CreateIngredientService extends AbstractController
{
  public function __construct(
    private EntityManagerInterface $em,
    private UnitRepository $ur,
    private IngredientDataRepository $idr,
    private IngredientTypeRepository $itr,
  ) {
  }

  public function createIngredient($ingredientItem, $newRecipe)
  {
    $ingredientLabel = ucfirst(strtolower($ingredientItem["label"]));
    $ingredient = new Ingredient();
    $ingredient->setQuantity($ingredientItem["quantity"]);
    $ingredient->setLabel(($ingredientLabel));
    $ingredient->setUnit($this->ur->find($ingredientItem["unit"]["id"]));
    $ingredient->setRecipe($newRecipe);

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

    $this->em->persist($ingredient);
  }
}
