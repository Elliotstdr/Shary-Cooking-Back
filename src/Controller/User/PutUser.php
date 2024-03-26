<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\PostImageService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class PutUser extends AbstractController
{
  public function __construct(
    private readonly PostImageService $pis,
    private readonly EntityManagerInterface $em,
    private readonly UserRepository $ur,
    private readonly JWTTokenManagerInterface $JWTManager
  ) {
  }

  public function __invoke(Request $request, int $id): JsonResponse
  {
    $requestData = json_decode($request->getContent(), true);
    $userToModify = $this->ur->find($id);
    $factory = new PasswordHasherFactory([
      'common' => ['algorithm' => 'bcrypt'],
    ]);
    $passwordHasher = $factory->getPasswordHasher('common');

    if ($userToModify->getEmail() === "test@test.com") {
      throw new Exception('Vous ne pouvez pas modifier les informations du compte avec un compte visiteur');
    }

    if (
      $userToModify->getEmail() !== $requestData["email"] &&
      $this->ur->findOneBy(['email' => $requestData["email"]])
    ) {
      throw new Exception('Cette adresse email est déjà utilisée pour un autre compte');
    }

    if (isset($requestData["image"]) && $requestData["image"]) {
      $fileName = $this->pis->saveFile($requestData["image"], 500, $userToModify->getImageUrl());
      $userToModify->setImageUrl('/media/' . $fileName);
      $this->em->persist($userToModify);
    }

    $token = null;
    if ($userToModify->getEmail() !== $requestData["email"]) {
      $tempUser = new User();
      $tempUser->setEmail($requestData["email"]);
      $token = $this->JWTManager->create($tempUser);
    }

    if (isset($requestData["oldPassword"])) {
      if (!$passwordHasher->verify($userToModify->getPassword(), $requestData["oldPassword"])) {
        throw new Exception('L\'ancien mot de passe est incorrect');
      } else {
        $userToModify->setPassword($passwordHasher->hash($requestData["password"]));
      }
    }

    $userToModify->setLastname($requestData["lastname"]);
    $userToModify->setName($requestData["name"]);
    $userToModify->setEmail($requestData["email"]);
    $this->em->persist($userToModify);
    $this->em->flush();

    return new JsonResponse(["imageUrl" => $userToModify->getImageUrl(), "token" => $token]);
  }
}
