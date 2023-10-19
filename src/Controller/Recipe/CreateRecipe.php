<?php

namespace App\Controller\Recipe;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\Step;
use App\Repository\RecipeRepository;
use App\Service\CreateIngredientService;
use App\Service\PostImageService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class CreateRecipe extends AbstractController
{
  public function __construct(
    private EntityManagerInterface $em,
    private RecipeRepository $rr,
    private PostImageService $postImageService,
    private CreateIngredientService $cis,
    private DenormalizerInterface $denormalizer
  ) {
  }

  public function __invoke(Request $request, Recipe $data)
  {
    if ($data->getPostedByUser()->getEmail() === "test@test.com") {
      throw new Exception('Vous ne pouvez pas créer de recette avec un compte visiteur');
    }
    $this->em->persist($data);
    $this->em->flush();

    $decodedResponse = json_decode($request->getContent(), true);
    $steps = $this->denormalizer->denormalize($decodedResponse["steps"], Step::class . "[]");
    $ingredients = $this->denormalizer->denormalize($decodedResponse["ingredients"], Ingredient::class . "[]");

    $newRecipe = $this->rr->find($data->getId());
    foreach ($steps as $step) {
      $step->setRecipe($newRecipe);
      $this->em->persist($step);
    }

    foreach ($ingredients as $ingredient) {
      $this->cis->createIngredient($ingredient, $newRecipe);
    }

    $this->em->flush();

    if (isset($decodedResponse["image"]) && $decodedResponse["image"]) {
      $fileName = $this->postImageService->saveFile($decodedResponse["image"]);
      $data->setImageUrl('/media/' . $fileName);
      $this->em->persist($data);
      $this->em->flush();
    }

    return $newRecipe;
  }
}
