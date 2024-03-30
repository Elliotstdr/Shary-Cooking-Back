<?php

namespace App\Command;

use App\Entity\ExternalToken;
use App\Repository\ExternalTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(name: 'app:external:token')]
class ExternalTokenCommand extends Command
{
  public function __construct(
    private readonly EntityManagerInterface $em,
    private readonly HttpClientInterface $httpClient,
    private readonly ExternalTokenRepository $externalTokenRepository,
    private readonly string $externalTokenUrl
  ) {
    parent::__construct();
  }

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    try {
      // Récupération de l'external token à partir de l'URL spécifiée
      $query = $this->httpClient->request('GET', $this->externalTokenUrl);

      // Récupération des tokens externes existants depuis la base de données
      $currentTokens = $this->externalTokenRepository->findAll();

      // Extraction du token à partir de la réponse HTTP
      $firstExplode = explode('access_token":"', $query->getContent());
      $secondExplode = explode('","expires_in', $firstExplode[1]);
      $token = $secondExplode[0];

      // Stockage du token externe dans la base de données
      if (count($currentTokens) === 0) {
        // Si aucun token n'est trouvé, création et persistation d'un nouvel objet ExternalToken
        $externalToken = new ExternalToken();
        $externalToken->setValue($token);
        $this->em->persist($externalToken);
      } else {
        // Mise à jour de la valeur du premier token existant avec la nouvelle valeur de token
        $currentTokens[0]->setValue($token);
      }

      // Enregistrement des modifications dans la base de données
      $this->em->flush();

      // Retourner le statut de succès de la commande
      return Command::SUCCESS;
    } catch (Exception $e) {
      // En cas d'exception, retourner le statut d'échec de la commande
      return Command::FAILURE;
    }
  }
}
