<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;

class CreateAccount extends AbstractController
{
  public function __construct()
  {
  }

  public function __invoke(User $data, EntityManagerInterface $em, UserRepository $ur)
  {
    $factory = new PasswordHasherFactory([
      'common' => ['algorithm' => 'bcrypt'],
    ]);
    $passwordHasher = $factory->getPasswordHasher('common');

    if ($ur->findOneBy(['email' => $data->getEmail()])) {
      throw new Exception('Cette adresse email est déjà utilisée pour un autre compte');
    }
    $data->setPassword($passwordHasher->hash($data->getPassword()));
    $em->persist($data);
    $em->flush();

    $returnUser = $ur->findOneBy(['email' => $data->getEmail()]);
    return [
      'id' => $returnUser->getId(),
      'name' => $returnUser->getName(),
      'lastname' => $returnUser->getLastname(),
      'email' => $returnUser->getEmail(),
      'imageUrl' => $returnUser->getImageUrl()
    ];
  }
}
