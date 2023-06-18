<?php

namespace App\Controller\Recipe;

use App\Dto\CreateRecipeDto;
use App\Entity\Ingredient;
use App\Entity\IngredientData;
use App\Entity\Recipe;
use App\Entity\Step;
use App\Repository\IngredientDataRepository;
use App\Repository\IngredientTypeRepository;
use App\Repository\RecipeRepository;
use App\Repository\UnitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class CreateRecipe extends AbstractController
{
  public function __construct(
    private EntityManagerInterface $em,
    private UnitRepository $ur,
    private RecipeRepository $rr,
    private IngredientDataRepository $idr,
    private IngredientTypeRepository $itr,
  ) {
  }

  public function __invoke(Request $request, SerializerInterface $serializer, Recipe $data)
  {
    $recetteCreateDto = $serializer->deserialize($request->getContent(), CreateRecipeDto::class, 'json');
    $this->em->persist($data);
    $this->em->flush();

    $newRecipe = $this->rr->find($data->getId());
    foreach ($recetteCreateDto->steps as $step) {
      $this->createStep($step, $newRecipe);
    }

    foreach ($recetteCreateDto->ingredients as $ingredient) {
      $this->createIngredient($ingredient, $newRecipe);
    }

    $this->em->flush();

    return $newRecipe;
  }

  public function createStep($stepItem, $newRecipe)
  {
    $step = new Step();
    $step->setStepIndex($stepItem["stepIndex"]);
    $step->setDescription(($stepItem["description"]));
    $step->setRecipe($newRecipe);

    $this->em->persist($step);
  }

  public function createIngredient($ingredientItem, $newRecipe)
  {
    $ingredient = new Ingredient();
    $ingredient->setQuantity($ingredientItem["quantity"]);
    $ingredient->setLabel(($ingredientItem["label"]));
    $ingredient->setUnit($this->ur->find($ingredientItem["unit"]["id"]));
    $ingredient->setRecipe($newRecipe);

    if (!$this->idr->findOneBy(["name" => $ingredientItem["label"]])) {
      $newIng = new IngredientData();
      $newIng->setName($ingredientItem["label"]);
      $newIng->setType($this->itr->findOneBy(["label" => "unknown"]));
      $this->em->persist($newIng);
    }

    $this->em->persist($ingredient);
  }
}