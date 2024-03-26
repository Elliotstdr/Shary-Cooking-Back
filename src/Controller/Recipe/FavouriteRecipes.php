<?php

namespace App\Controller\Recipe;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FavouriteRecipes extends AbstractController
{
  public function __construct(
    private readonly RecipeRepository $rr
  ) {
  }
  /**
   * @param int $id
   * @return Recipe[]|null
   */
  public function __invoke(int $id): ?array
  {
    return $this->rr->getFavouriteRecipes($id);
  }
}
