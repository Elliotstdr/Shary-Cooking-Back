<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserByEmail extends AbstractController
{
  public function __construct(
    private TokenStorageInterface $tokenStorage,
    private UserRepository $userRepository
  ) {
  }

  public function __invoke()
  {
    $tokenIdentifier = $this->tokenStorage->getToken()->getUserIdentifier();

    $user = $this->userRepository->findOneBy(["email" => $tokenIdentifier]);

    return $user;
  }
}
