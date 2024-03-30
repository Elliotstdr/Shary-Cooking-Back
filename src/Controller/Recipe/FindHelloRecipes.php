<?php

namespace App\Controller\Recipe;

use App\Repository\ExternalTokenRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FindHelloRecipes extends AbstractController
{
  public function __construct(
    private readonly HttpClientInterface $httpClient,
    private readonly ExternalTokenRepository $externalTokenRepository,
    private readonly string $hfRecipeUrl,
    private readonly string $hfImageUrl
  ) {
  }

  public function __invoke(Request $request)
  {
    try {
      $currentTokens = $this->externalTokenRepository->findAll();
      $decodedRequest = json_decode($request->getContent(), true);

      if (count($currentTokens) === 0) {
        throw new Exception;
      } else {
        $token = $currentTokens[0]->getValue();
      }

      $query = $this->httpClient->request(
        'GET',
        $this->hfRecipeUrl . '&q=' . $decodedRequest["search"],
        ['auth_bearer' => $token]
      );

      $data = json_decode($query->getContent(), true);

      $response = [];

      foreach ($data["items"] as $item) {
        $response[] = [
          "id" => $item["id"],
          "imagePath" => $this->hfImageUrl . $item["imagePath"],
          "name" => $item["name"],
          "prepTime" => $item["prepTime"],
          "ingredients" => $item["ingredients"],
          "steps" => $item["steps"],
          "tags" => $item["tags"],
          "yields" => $item["yields"],
          "averageRating" => $item["averageRating"],
        ];
      }

      return new JsonResponse($response);
    } catch (Exception $e) {
      throw $e;
    }
  }
}
