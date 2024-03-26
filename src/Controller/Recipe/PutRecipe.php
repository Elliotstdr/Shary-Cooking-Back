<?php

namespace App\Controller\Recipe;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\Step;
use App\Service\CreateIngredientService;
use App\Service\PostImageService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class PutRecipe extends AbstractController
{
  public function __construct(
    private readonly EntityManagerInterface $em,
    private readonly PostImageService $pis,
    private readonly CreateIngredientService $cis,
    private readonly DenormalizerInterface $denormalizer
  ) {
  }

  public function __invoke(Request $request, Recipe $data): Recipe
  {
    if ($data->getPostedByUser()->getEmail() === "test@test.com") {
      throw new Exception('Vous ne pouvez pas modifier cette recette avec un compte visiteur');
    }
    $this->em->persist($data);
    $data->removeAllSteps();
    $data->removeAllIngredients();
    $this->em->flush();

    $decodedResponse = json_decode($request->getContent(), true);
    $steps = $this->denormalizer->denormalize($decodedResponse["steps"], Step::class . "[]");
    $ingredients = $this->denormalizer->denormalize($decodedResponse["ingredients"], Ingredient::class . "[]");

    foreach ($steps as $step) {
      $step->setRecipe($data);
      $this->em->persist($step);
    }
    foreach ($ingredients as $ingredient) {
      $this->cis->createIngredient($ingredient, $data);
    }

    if (isset($decodedResponse["image"]) && $decodedResponse["image"]) {
      $fileName = $this->pis->saveFile($decodedResponse["image"], 1200, $data->getImageUrl());
      $data->setImageUrl('/media/' . $fileName);
      $this->em->persist($data);
    }

    $this->em->flush();

    return $data;
  }
}
