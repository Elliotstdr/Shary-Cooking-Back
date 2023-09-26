<?php

namespace App\Controller\Recipe;

use App\Dto\CreateRecipeDto;
use App\Entity\Recipe;
use App\Service\CreateIngredientService;
use App\Service\CreateStepService;
use App\Service\PostImageService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class PutRecipe extends AbstractController
{
  public function __construct(
    private EntityManagerInterface $em,
    private PostImageService $pis,
    private CreateIngredientService $cis,
    private CreateStepService $css
  ) {
  }

  public function __invoke(Request $request, SerializerInterface $serializer, Recipe $data)
  {
    if ($data->getPostedByUser()->getEmail() === "test@test.com") {
      throw new Exception('Vous ne pouvez pas modifier cette recette avec un compte visiteur');
    }
    $recettePutDto = $serializer->deserialize($request->getContent(), CreateRecipeDto::class, 'json');
    $this->em->persist($data);

    $data->removeAllSteps();
    $data->removeAllIngredients();
    $this->em->flush();

    foreach ($recettePutDto->steps as $step) {
      $this->css->createStep($step, $data);
    }
    foreach ($recettePutDto->ingredients as $ingredient) {
      $this->cis->createIngredient($ingredient, $data);
    }

    if ($recettePutDto->image) {
      $fileName = $this->pis->saveFile($recettePutDto->image, 1200, $data->getImageUrl());
      $data->setImageUrl('/media/' . $fileName);
      $this->em->persist($data);
    }

    $this->em->flush();

    return $data;
  }
}
