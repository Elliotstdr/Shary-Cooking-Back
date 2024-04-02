<?php

namespace App\Service;

use App\Entity\Recipe;
use App\Entity\Step;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class CreateStepService extends AbstractController
{
  public function __construct(
    private readonly EntityManagerInterface $em,
    private readonly DenormalizerInterface $denormalizer
  ) {
  }

  public function createStep(array $decodedSteps, Recipe $newRecipe)
  {
    /**
     * @var Step[]
     */
    $steps = $this->denormalizer->denormalize($decodedSteps, Step::class . "[]");

    foreach ($steps as $step) {
      $step->setRecipe($newRecipe);
      $this->em->persist($step);
    }
  }
}
