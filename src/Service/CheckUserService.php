<?php

namespace App\Service;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CheckUserService extends AbstractController
{
  public function __construct(
    private TokenStorageInterface $tokenStorage,
    private UserRepository $userRepository
  ) {
  }

  public function checkUser(Request $request): bool
  {
    $tokenIdentifier = $this->tokenStorage->getToken()->getUserIdentifier();

    $userRequested = $this->userRepository->find($request->attributes->get('id'));

    return $userRequested && $tokenIdentifier === $userRequested->getEmail();
  }
}
