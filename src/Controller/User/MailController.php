<?php

namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailController extends AbstractController
{
  public function __construct()
  {
  }

  public function __invoke(Request $request, MailerInterface $mailer): Response
  {
    $requestData = json_decode($request->getContent(), true);
    // Récupérer les données du formulaire depuis la requête
    $title = $requestData['title'];
    $firstname = $requestData['firstname'];
    $lastname = $requestData['lastname'];
    $message = $requestData['message'];
    $file = $requestData['file'];

    $fileData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $file));

    // Envoyer l'e-mail
    $email = (new Email())
      ->from('gece4010_mailsc@shary-cooking.fr')
      ->to('gece4010_mailsc@shary-cooking.fr')
      ->subject('Nouveau message de contact')
      ->html("$firstname $lastname <br> Titre : $title <br> Message : $message")
      ->attach($fileData, 'capture.png');

    $mailer->send($email);

    // Répondre avec une réponse JSON pour confirmer l'envoi de l'e-mail
    return $this->json(['message' => 'E-mail envoyé avec succès']);
  }
}
