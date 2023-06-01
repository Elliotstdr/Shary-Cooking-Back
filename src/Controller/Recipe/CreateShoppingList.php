<?php

namespace App\Controller\Recipe;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CreateShoppingList extends AbstractController
{
  public function __construct(public RecipeRepository $rr)
  {
  }

  public function __invoke(Request $request)
  {
    $jsonData = json_decode($request->getContent(), true);
    $exportArray = [];
    foreach ($jsonData["data"] as $element) {
      $exportArray[] = $element["quantity"] . $element["unit"]["label"] . " " . $element["label"];
    }
    $data = implode("\n", $exportArray);

    $response = new Response();
    $response->headers->set('Content-Type', 'text/plain');
    $response->setContent($data);
    $response->headers->set('Content-Disposition', 'attachment; filename=MyShoppingList.txt');

    return $response;
  }
}
