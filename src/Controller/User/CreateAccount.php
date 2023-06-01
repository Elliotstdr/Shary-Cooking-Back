<?php

namespace App\Controller\User;

use App\Dto\CreateAccountDto;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\Serializer\SerializerInterface;

class CreateAccount extends AbstractController
{
  public function __construct(
    private EntityManagerInterface $em,
    private UserRepository $ur,
  ) {
  }

  public function __invoke(User $data, SerializerInterface $serializer, Request $request)
  {
    $accountDto = $serializer->deserialize($request->getContent(), CreateAccountDto::class, 'json');

    $factory = new PasswordHasherFactory([
      'common' => ['algorithm' => 'bcrypt'],
    ]);
    $passwordHasher = $factory->getPasswordHasher('common');

    if (!$passwordHasher->verify($this->getParameter('secretKey'), $accountDto->secretKey)) {
      throw new Exception('La clef secrète que vous avez renseigné est incorrecte.');
    }

    if ($this->ur->findOneBy(['email' => $data->getEmail()])) {
      throw new Exception('Cette adresse email est déjà utilisée pour un autre compte');
    }
    $data->setPassword($passwordHasher->hash($data->getPassword()));
    $this->em->persist($data);
    $this->em->flush();

    $returnUser = $this->ur->findOneBy(['email' => $data->getEmail()]);
    return [
      'id' => $returnUser->getId(),
      'name' => $returnUser->getName(),
      'lastname' => $returnUser->getLastname(),
      'email' => $returnUser->getEmail(),
      'imageUrl' => $returnUser->getImageUrl()
    ];
  }
}
