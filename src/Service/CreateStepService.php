<?php

namespace App\Service;

use App\Entity\Step;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CreateStepService extends AbstractController
{
  public function __construct(
    private EntityManagerInterface $em,
  ) {
  }

  public function createStep($stepItem, $newRecipe)
  {
    $step = new Step();
    $step->setStepIndex($stepItem["stepIndex"]);
    $step->setDescription(($stepItem["description"]));
    $step->setRecipe($newRecipe);

    $this->em->persist($step);
  }
}
