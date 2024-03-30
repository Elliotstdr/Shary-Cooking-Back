<?php

namespace App\Controller\User;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;

class EditPassword extends AbstractController
{
  public function __construct(
    private readonly EntityManagerInterface $entityManager,
    private readonly UserRepository $userRepository,
  ) {
  }

  public function __invoke(Request $request, int $id): JsonResponse
  {
    $requestData = json_decode($request->getContent(), true);
    $userToModify = $this->userRepository->find($id);
    $factory = new PasswordHasherFactory([
      'common' => ['algorithm' => 'bcrypt'],
    ]);
    $passwordHasher = $factory->getPasswordHasher('common');

    if (isset($requestData["oldPassword"])) {
      if (!$passwordHasher->verify($userToModify->getPassword(), $requestData["oldPassword"])) {
        throw new Exception('L\'ancien mot de passe est incorrect');
      } else {
        $userToModify->setPassword($passwordHasher->hash($requestData["password"]));
      }
    }

    $this->entityManager->persist($userToModify);
    $this->entityManager->flush();

    return new JsonResponse("Succès");
  }
}
