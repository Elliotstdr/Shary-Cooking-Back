<?php

namespace App\Controller\Recipe;

use App\Dto\CreateRecipeDto;
use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use App\Service\CreateIngredientService;
use App\Service\CreateStepService;
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
    private RecipeRepository $rr,
    private PostImageService $postImageService,
    private CreateIngredientService $cis,
    private CreateStepService $css
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
      $this->css->createStep($step, $newRecipe);
    }

    foreach ($recetteCreateDto->ingredients as $ingredient) {
      $this->cis->createIngredient($ingredient, $newRecipe);
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
}
