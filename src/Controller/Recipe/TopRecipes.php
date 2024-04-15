<?php

namespace App\Controller\Recipe;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TopRecipes extends AbstractController
{
  public function __construct(
    private readonly RecipeRepository $recipeRepository
  ) {
  }

  public function __invoke()
  {
    return $this->recipeRepository->findTopRecipes();
  }
}
