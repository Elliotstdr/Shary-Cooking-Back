<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;

class CreateAccount extends AbstractController
{
  public function __construct(
    private readonly EntityManagerInterface $em,
    private readonly UserRepository $userRepository,
  ) {
  }

  public function __invoke(User $data, Request $request): User
  {
    $requestData = json_decode($request->getContent(), true);

    $factory = new PasswordHasherFactory([
      'common' => ['algorithm' => 'bcrypt'],
    ]);
    $passwordHasher = $factory->getPasswordHasher('common');

    if (!$passwordHasher->verify($this->getParameter('secretKey'), $requestData["secretKey"])) {
      throw new Exception('La clef secrète que vous avez renseigné est incorrecte.');
    }

    if ($this->userRepository->findOneBy(['email' => $data->getEmail()])) {
      throw new Exception('Cette adresse email est déjà utilisée pour un autre compte');
    }
    $data->setPassword($passwordHasher->hash($data->getPassword()));
    $this->em->persist($data);
    $this->em->flush();

    return $data;
  }
}
