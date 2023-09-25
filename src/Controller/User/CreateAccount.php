<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;

class CreateAccount extends AbstractController
{
  public function __construct(
    private EntityManagerInterface $em,
    private UserRepository $ur,
  ) {
  }

  public function __invoke(User $data, Request $request)
  {
    $requestData = json_decode($request->getContent(), true);

    $factory = new PasswordHasherFactory([
      'common' => ['algorithm' => 'bcrypt'],
    ]);
    $passwordHasher = $factory->getPasswordHasher('common');

    if (!$passwordHasher->verify($this->getParameter('secretKey'), $requestData["secretKey"])) {
      throw new Exception('La clef secrète que vous avez renseigné est incorrecte.');
    }

    if ($this->ur->findOneBy(['email' => $data->getEmail()])) {
      throw new Exception('Cette adresse email est déjà utilisée pour un autre compte');
    }
    $data->setPassword($passwordHasher->hash($data->getPassword()));
    $this->em->persist($data);
    $this->em->flush();

    $user = $this->ur->findOneBy(['email' => $data->getEmail()]);
    if (!$user) {
      return new JsonResponse("An error occured");
    }
    return $user;
  }
}
