<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class LoginCheck extends AbstractController
{
  public function __construct(
    private readonly UserRepository $userRepository,
    private readonly JWTTokenManagerInterface $JWTManager
  ) {
  }

  public function __invoke(User $data): JsonResponse
  {
    $foundedUser = $this->userRepository->findOneBy(['email' => $data->getEmail()]);
    if ($foundedUser) {
      return new JsonResponse($this->JWTManager->create($data));
    } else {
      return new JsonResponse("No token available");
    }
  }
}
