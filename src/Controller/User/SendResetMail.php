<?php

namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;

class SendResetMail extends AbstractController
{
  public function __construct(
    private readonly UserRepository $ur,
    private readonly MailerInterface $mailer,
    private readonly EntityManagerInterface $em,
  ) {
  }

  public function __invoke(Request $request): JsonResponse
  {
    $response = "Un mail avec une clé de réinitialisation vous a été envoyé";
    $requestData = json_decode($request->getContent(), true);
    $factory = new PasswordHasherFactory([
      'common' => ['algorithm' => 'bcrypt'],
    ]);
    $passwordHasher = $factory->getPasswordHasher('common');
    $user = $this->ur->findOneBy(['email' => $requestData["email"]]);

    if (!$user) {
      return new JsonResponse($response);
    }

    $resetKey = bin2hex(random_bytes(10));
    $hashedResetString = $passwordHasher->hash($resetKey);
    $user->setResetPassword($hashedResetString);
    $this->em->persist($user);
    $this->em->flush();

    $email = (new Email())
      ->from('noreply@shary-cooking.fr')
      ->to($requestData["email"])
      ->subject('Réinitialisation de votre mot de passe')
      ->html("Voici votre clé de réinitialisation : <br> $resetKey");

    $$this->mailer->send($email);

    return new JsonResponse($response);
  }
}
