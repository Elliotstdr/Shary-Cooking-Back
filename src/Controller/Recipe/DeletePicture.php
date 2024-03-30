<?php

namespace App\Controller\Recipe;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use App\Service\DeleteOldFileService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DeletePicture extends AbstractController
{
  public function __construct(
    private readonly RecipeRepository $recipeRepository,
    private readonly EntityManagerInterface $entityManager,
    private readonly DeleteOldFileService $deleteOldFileService
  ) {
  }

  public function __invoke(int $id): Recipe
  {
    $recipe = $this->recipeRepository->find($id);

    if (!$recipe) {
      throw new Exception("No recipe found");
    }

    if ($recipe->isFromHellof()) {
      throw new Exception("Picture delete not allowed");
    }

    $this->deleteOldFileService->deleteOldFile($recipe->getImageUrl());

    $recipe->setImage(null);
    $recipe->setImageUrl(null);

    $this->entityManager->persist($recipe);
    $this->entityManager->flush();

    return $recipe;
  }
}
