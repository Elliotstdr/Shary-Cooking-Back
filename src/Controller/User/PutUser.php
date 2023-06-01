<?php

namespace App\Controller\User;

use App\Dto\PutUserDto;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\Serializer\SerializerInterface;

class PutUser extends AbstractController
{
  public function __construct()
  {
  }

  public function __invoke(Request $request, int $id, EntityManagerInterface $em, UserRepository $ur, SerializerInterface $serializer)
  {
    $putUserDto = $serializer->deserialize($request->getContent(), PutUserDto::class, 'json');
    $userToModify = $ur->find($id);
    $factory = new PasswordHasherFactory([
      'common' => ['algorithm' => 'bcrypt'],
    ]);
    $passwordHasher = $factory->getPasswordHasher('common');

    if (
      $userToModify->getEmail() !== $putUserDto->email &&
      $ur->findOneBy(['email' => $putUserDto->email])
    ) {
      throw new Exception('Cette adresse email est déjà utilisée pour un autre compte');
    }

    if ($putUserDto->oldPassword) {
      if (!$passwordHasher->verify($userToModify->getPassword(), $putUserDto->oldPassword)) {
        throw new Exception('L\'ancien mot de passe sont incorrect');
      } else {
        $userToModify->setPassword($passwordHasher->hash($putUserDto->password));
      }
    }

    $userToModify->setLastname($putUserDto->name);
    $userToModify->setName($putUserDto->lastname);
    $userToModify->setEmail($putUserDto->email);

    $em->persist($userToModify);
    $em->flush();

    return new JsonResponse("Vos informations ont bien été modifiées");
  }
}
