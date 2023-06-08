<?php

namespace App\Controller\Recipe;

use App\Dto\SaveRecipeDto;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class SaveRecipe extends AbstractController
{
  public function saveRecipe(Request $request, $userId, $recipeId, UserRepository $ur, RecipeRepository $rr, EntityManagerInterface $em, SerializerInterface $serializer)
  {
    $recetteCreateDto = $serializer->deserialize($request->getContent(), SaveRecipeDto::class, 'json');
    $recipe = $rr->find($recipeId);
    $user = $ur->find($userId);

    if ($recetteCreateDto->action === "add") {
      $user->addSavedRecipe($recipe);
      $em->persist($user);
      $em->flush();
    }

    if ($recetteCreateDto->action === "delete") {
      $user->removeSavedRecipe($recipe);
      $em->flush();
    }

    return new JsonResponse("Success");
  }
}
