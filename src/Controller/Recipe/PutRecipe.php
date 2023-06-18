<?php

namespace App\Controller\Recipe;

use App\Dto\CreateRecipeDto;
use App\Entity\Recipe;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class PutRecipe extends AbstractController
{
  public function __construct(
    private EntityManagerInterface $em,
    private CreateRecipe $cr,
  ) {
  }

  public function __invoke(Request $request, SerializerInterface $serializer, Recipe $data)
  {
    $recettePutDto = $serializer->deserialize($request->getContent(), CreateRecipeDto::class, 'json');
    $this->em->persist($data);

    foreach ($data->getIngredients() as $ingredient) {
      $this->em->remove($ingredient);
    }
    foreach ($data->getSteps() as $step) {
      $this->em->remove($step);
    }

    foreach ($recettePutDto->steps as $step) {
      $this->cr->createStep($step, $data);
    }
    foreach ($recettePutDto->ingredients as $ingredient) {
      $this->cr->createIngredient($ingredient, $data);
    }

    $this->em->flush();

    return $data;
  }
}
