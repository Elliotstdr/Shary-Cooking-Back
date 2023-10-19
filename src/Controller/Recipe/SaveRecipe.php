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
  public function __construct(
    private RecipeRepository $rr,
    private UserRepository $ur,
    private EntityManagerInterface $em
  ) {
  }

  public function __invoke(Request $request, $userId, $recipeId)
  {
    $requestData = json_decode($request->getContent(), true);
    $recipe = $this->rr->find($recipeId);
    $user = $this->ur->find($userId);

    if ($requestData["action"] === "add") {
      $user->addSavedRecipe($recipe);
    }

    if ($requestData["action"] === "delete") {
      $user->removeSavedRecipe($recipe);
    }

    $this->em->persist($user);
    $this->em->flush();

    return new JsonResponse("Success");
  }
}
