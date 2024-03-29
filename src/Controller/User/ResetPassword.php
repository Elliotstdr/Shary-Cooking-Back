<?php

namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ResetPassword extends AbstractController
{
  public function __construct(
    private readonly UserRepository $userRepository,
    private readonly EntityManagerInterface $em,
    private readonly JWTTokenManagerInterface $JWTManager,
    private readonly NormalizerInterface $normalizer
  ) {
  }

  public function __invoke(Request $request): JsonResponse
  {
    $requestData = json_decode($request->getContent(), true);
    $factory = new PasswordHasherFactory([
      'common' => ['algorithm' => 'bcrypt'],
    ]);
    $pHasher = $factory->getPasswordHasher('common');

    $user = $this->userRepository->findOneBy(['email' => $requestData["email"]]);

    if ($user && !$pHasher->verify($user->getResetPassword(), $requestData["resetKey"])) {
      throw new Exception("La clé de réinitialisation n'est pas correcte");
    }

    $user->setResetPassword(null);
    $user->setPassword($pHasher->hash($requestData["newPassword"]));
    $this->em->persist($user);
    $this->em->flush();

    return new JsonResponse([
      "user" => $this->normalizer->normalize($user), "token" => $this->JWTManager->create($user)
    ]);
  }
}
