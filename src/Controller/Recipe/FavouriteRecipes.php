<?php

namespace App\Controller\Recipe;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FavouriteRecipes extends AbstractController
{
  public function __construct(
    public RecipeRepository $rr
  ) {
  }
  /**
   * @param int $id
   */
  public function __invoke(int $id)
  {
    return $this->rr->getFavouriteRecipes($id);
  }
}
