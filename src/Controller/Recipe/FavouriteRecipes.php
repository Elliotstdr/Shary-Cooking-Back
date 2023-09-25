<?php

namespace App\Controller\Recipe;

use App\Repository\RecipeRepository;
use App\Service\CheckUserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class FavouriteRecipes extends AbstractController
{
  public function __construct(
    public RecipeRepository $rr,
    private CheckUserService $checkUserService
  ) {
  }
  /**
   * @param int $id
   */
  public function __invoke(Request $request, int $id)
  {
    if (!$this->checkUserService->checkUser($request)) {
      return new JsonResponse('Accès non autorisé', 403);
    }
    $data = $this->rr->getFavouriteRecipes($id);

    $response = [];
    foreach ($data as $element) {
      $response[] = $this->rr->find($element["id"]);
    }
    return $response;
  }
}
