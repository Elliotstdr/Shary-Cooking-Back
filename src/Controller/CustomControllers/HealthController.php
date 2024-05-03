<?php

namespace App\Controller\CustomControllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/health', name: 'health', methods: ['GET'])]
class HealthController extends AbstractController
{
  public function __construct()
  {
  }

  public function __invoke(): JsonResponse
  {
    return new JsonResponse("Success");
  }
}
