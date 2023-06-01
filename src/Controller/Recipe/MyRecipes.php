<?php

namespace App\Controller\Recipe;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MyRecipes extends AbstractController
{
  public function __construct(public RecipeRepository $rr)
  {
  }
  /**
   * @param int $id
   */
  public function __invoke(int $id)
  {
    $data = $this->rr->getMyRecipes($id);

    $response = [];
    foreach ($data as $element) {
      $response[] = $this->rr->find($element["id"]);
    }
    return $response;
  }
}
