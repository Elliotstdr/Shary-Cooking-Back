<?php

namespace App\Service;

use App\Entity\Ingredient;
use App\Entity\IngredientData;
use App\Entity\Recipe;
use App\Repository\IngredientDataRepository;
use App\Repository\IngredientTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class CreateIngredientService extends AbstractController
{
  public function __construct(
    private readonly EntityManagerInterface $em,
    private readonly IngredientDataRepository $ingredientDataRepository,
    private readonly IngredientTypeRepository $ingredientTypeRepository,
    private readonly DenormalizerInterface $denormalizer
  ) {
  }

  public function createIngredient(array $decodedIngredients, Recipe $newRecipe): void
  {
    /**
     * @var Ingredient[]
     */
    $ingredients = $this->denormalizer->denormalize($decodedIngredients, Ingredient::class . "[]");

    foreach ($ingredients as $ingredient) {
      $ingredientLabel = ucfirst(strtolower($ingredient->getLabel()));
      $ingredient->setLabel(($ingredientLabel));
      $ingredient->setRecipe($newRecipe);

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

      $this->em->persist($ingredient);
    }
  }
}
