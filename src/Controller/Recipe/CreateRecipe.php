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
use App\Service\PostImageService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
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
    private PostImageService $postImageService,
  ) {
  }

  public function __invoke(Request $request, SerializerInterface $serializer, Recipe $data)
  {
    if ($data->getPostedByUser()->getEmail() === "test@test.com") {
      throw new Exception('Vous ne pouvez pas créer de recette avec un compte visiteur');
    }
    $this->em->persist($data);
    $this->em->flush();

    $recetteCreateDto = $serializer->deserialize($request->getContent(), CreateRecipeDto::class, 'json');

    $newRecipe = $this->rr->find($data->getId());
    foreach ($recetteCreateDto->steps as $step) {
      $this->createStep($step, $newRecipe);
    }

    foreach ($recetteCreateDto->ingredients as $ingredient) {
      $this->createIngredient($ingredient, $newRecipe);
    }

    $this->em->flush();

    if ($recetteCreateDto->image) {
      $fileName = $this->postImageService->saveFile($recetteCreateDto->image);
      $data->setImageUrl('/media/' . $fileName);
      $this->em->persist($data);
      $this->em->flush();
    }

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
