<?php

namespace App\Controller\Recipe;

use App\Entity\Recipe;
use App\Service\CreateIngredientService;
use App\Service\CreateStepService;
use App\Service\PostImageService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class PutRecipe extends AbstractController
{
  public function __construct(
    private readonly EntityManagerInterface $em,
    private readonly PostImageService $postImageService,
    private readonly CreateIngredientService $createIngredientService,
    private readonly CreateStepService $createStepService,
    private readonly DenormalizerInterface $denormalizer
  ) {
  }

  public function __invoke(Request $request, Recipe $data): Recipe
  {
    $this->em->beginTransaction();
    try {
      $this->em->persist($data);
      $data->removeAllSteps();
      $data->removeAllIngredients();
      $this->em->flush();

      $req = json_decode($request->getContent(), true);
      $this->createStepService->createStep($req["steps"], $data);
      $this->createIngredientService->createIngredient($req["ingredients"], $data);

      if (!$data->isFromHellof() && isset($req["image"]) && $req["image"]) {
        $fileName = $this->postImageService->saveFile($req["image"], 1200, $data->getImageUrl());
        $data->setImageUrl($fileName);
        $this->em->persist($data);
      }

      $this->em->flush();
      $this->em->commit();

      return $data;
    } catch (Exception $e) {
      $this->em->rollback();
      throw new Exception($e);
    }
  }
}
