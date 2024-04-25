<?php

namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailController extends AbstractController
{
  public function __construct(
    private readonly MailerInterface $mailer,
    private readonly string $email
  ) {
  }

  public function __invoke(Request $request): Response
  {
    $requestData = json_decode($request->getContent(), true);
    // Récupérer les données du formulaire depuis la requête
    $title = $requestData['title'];
    $firstname = $requestData['firstname'];
    $lastname = $requestData['lastname'];
    $message = $requestData['message'];
    $file = $requestData['image'];

    // Envoyer l'e-mail
    $email = (new Email())
      ->from('no-reply@shary-cooking.fr')
      ->to($this->email)
      ->subject('Bug report')
      ->html("$firstname $lastname <br> Titre : $title <br> Message : $message");

    if ($file) {
      $fileData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $file));
      $email->attach($fileData, 'capture.png');
    }

    $this->mailer->send($email);

    // Répondre avec une réponse JSON pour confirmer l'envoi de l'e-mail
    return $this->json(['message' => 'E-mail envoyé avec succès']);
  }
}
