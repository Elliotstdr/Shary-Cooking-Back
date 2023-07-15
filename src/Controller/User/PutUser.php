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
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class PutUser extends AbstractController
{
  public function __construct()
  {
  }

  public function __invoke(Request $request, int $id, EntityManagerInterface $em, UserRepository $ur, JWTTokenManagerInterface $JWTManager)
  {
    $requestData = json_decode($request->getContent(), true);
    $userToModify = $ur->find($id);
    $factory = new PasswordHasherFactory([
      'common' => ['algorithm' => 'bcrypt'],
    ]);
    $passwordHasher = $factory->getPasswordHasher('common');

    if (
      $userToModify->getEmail() !== $requestData["email"] &&
      $ur->findOneBy(['email' => $requestData["email"]])
    ) {
      throw new Exception('Cette adresse email est déjà utilisée pour un autre compte');
    }

    $token = null;
    if ($userToModify->getEmail() !== $requestData["email"]) {
      $tempUser = new User();
      $tempUser->setEmail($requestData["email"]);
      $token = $JWTManager->create($tempUser);
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
    $em->persist($userToModify);
    $em->flush();

    return new JsonResponse(["Vos informations ont bien été modifiées", $token]);
  }
}
