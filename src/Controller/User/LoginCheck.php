<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;

class LoginCheck extends AbstractController
{
  public function __construct()
  {
  }

  public function __invoke(User $data, UserRepository $ur)
  {
    $factory = new PasswordHasherFactory([
      'common' => ['algorithm' => 'bcrypt'],
    ]);
    $passwordHasher = $factory->getPasswordHasher('common');

    $foundedUser = $ur->findOneBy(['email' => $data->getEmail()]);
    if (!$foundedUser || !$passwordHasher->verify($foundedUser->getPassword(), $data->getPassword())) {
      throw new Exception('L\'adresse mail ou le mot de passe sont incorrect');
    }
    return [
      'id' => $foundedUser->getId(),
      'name' => $foundedUser->getName(),
      'lastname' => $foundedUser->getLastname(),
      'email' => $foundedUser->getEmail(),
      'imageUrl' => $foundedUser->getImageUrl()
    ];
  }
}
