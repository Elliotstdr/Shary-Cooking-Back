<?php

namespace App\Controller\Recipe;

use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SaveRecipe extends AbstractController
{
  public function saveRecipe(Request $request, $userId, $recipeId, UserRepository $ur, RecipeRepository $rr, EntityManagerInterface $em)
  {
    $requestData = json_decode($request->getContent(), true);
    $recipe = $rr->find($recipeId);
    $user = $ur->find($userId);

    if ($requestData["action"] === "add") {
      $user->addSavedRecipe($recipe);
      $em->persist($user);
      $em->flush();
    }

    if ($requestData["action"] === "delete") {
      $user->removeSavedRecipe($recipe);
      $em->flush();
    }

    return new JsonResponse("Success");
  }
}
