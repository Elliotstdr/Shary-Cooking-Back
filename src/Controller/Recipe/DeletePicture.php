<?php

namespace App\Controller\Recipe;

use App\Repository\RecipeRepository;
use App\Service\DeleteOldFileService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DeletePicture extends AbstractController
{
  public function __construct(
    private RecipeRepository $recipeRepository,
    private EntityManagerInterface $entityManager,
    private DeleteOldFileService $deleteOldFileService
  ) {
  }

  public function __invoke($id)
  {
    $recipe = $this->recipeRepository->find($id);

    if (!$recipe) {
      throw new Exception("No recipe found");
    }

    $this->deleteOldFileService->deleteOldFile($recipe->getImageUrl());

    $recipe->setImage(null);
    $recipe->setImageUrl(null);

    $this->entityManager->persist($recipe);
    $this->entityManager->flush();

    return $recipe;
  }
}
