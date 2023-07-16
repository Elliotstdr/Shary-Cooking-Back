<?php

namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;

class SendResetMail extends AbstractController
{
  public function __construct(
    private UserRepository $ur,
    private MailerInterface $mailer,
    private EntityManagerInterface $em,
  ) {
  }

  public function __invoke(Request $request, MailerInterface $mailer): JsonResponse
  {
    $requestData = json_decode($request->getContent(), true);
    $factory = new PasswordHasherFactory([
      'common' => ['algorithm' => 'bcrypt'],
    ]);
    $passwordHasher = $factory->getPasswordHasher('common');
    $user = $this->ur->findOneBy(['email' => $requestData["email"]]);

    if (!$passwordHasher->verify($this->getParameter('secretKey'), $requestData["secretKey"])) {
      throw new Exception('La clef secrète que vous avez renseigné est incorrecte.');
    }

    if (!$user) {
      throw new Exception("Le mail que vous avez fournit ne correspond à aucun.e utilisateurice");
    }

    $hashedResetString = $passwordHasher->hash(bin2hex(random_bytes(5)));
    $user->setPassword($hashedResetString);
    $this->em->persist($user);
    $this->em->flush();

    $email = (new Email())
      ->from('noreply@shary-cooking.fr')
      ->to($requestData["email"])
      ->subject('Réinitialisation de votre mot de passe')
      ->html("Voici votre clé de réinitialisation : <br> $hashedResetString");

    $mailer->send($email);

    return new JsonResponse("Un mail avec une clé de réinitialisation vous a été envoyé");
  }
}
