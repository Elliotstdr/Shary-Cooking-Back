<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class LoginCheck extends AbstractController
{
  public function __construct()
  {
  }

  public function __invoke(User $data, UserRepository $ur, JWTTokenManagerInterface $JWTManager): JsonResponse
  {
    $foundedUser = $ur->findOneBy(['email' => $data->getEmail()]);
    if ($foundedUser) {
      return new JsonResponse($JWTManager->create($data));
    } else {
      return new JsonResponse("No token available");
    }
  }
}
