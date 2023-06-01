<?php

namespace App\Controller\Recipe;

use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class SaveRecipe extends AbstractController
{
  public function saveRecipe($userId, $recipeId, UserRepository $ur, RecipeRepository $rr, EntityManagerInterface $em)
  {
    $recipe = $rr->find($recipeId);
    $user = $ur->find($userId);
    $user->addSavedRecipe($recipe);
    $em->persist($user);
    $em->flush();

    return new JsonResponse("Success");
  }
}
