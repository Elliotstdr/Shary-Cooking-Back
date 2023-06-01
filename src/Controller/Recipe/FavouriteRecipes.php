<?php

namespace App\Controller\Recipe;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class FavouriteRecipes extends AbstractController
{
  public function __construct(public RecipeRepository $rr)
  {
  }
  /**
   * @param int $id
   */
  public function __invoke(int $id)
  {
    $data = $this->rr->getFavouriteRecipes($id);
    return new JsonResponse($data);
  }
}
