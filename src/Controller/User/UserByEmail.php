<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserByEmail extends AbstractController
{
  public function __construct(
    private readonly TokenStorageInterface $tokenStorage,
    private readonly UserRepository $userRepository
  ) {
  }

  public function __invoke(): User
  {
    $tokenIdentifier = $this->tokenStorage->getToken()->getUserIdentifier();

    return $this->userRepository->findOneBy(["email" => $tokenIdentifier]);
  }
}
