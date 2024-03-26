<?php

namespace App\Command;

use App\Entity\IngredientType;
use App\Entity\Regime;
use App\Entity\Type;
use App\Entity\Unit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
  name: 'app:fixtures:all',
  description: 'Load all fixtures',
)]
class FixturesAll extends Command
{
  public function __construct(
    private EntityManagerInterface $em,
  ) {
    parent::__construct();
  }

  protected function configure(): void
  {
  }

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $io = new SymfonyStyle($input, $output);

    $regimes = ["Omnivore", "Végétarien", "Végétalien", "Végan"];
    $types = ["Entrée", "Plat", "Dessert", "Apéritif", "Goûter"];
    $units = ["milligrammes", "grammes", "kilogramme", "millilitres", "centilitres", "litre", "cc", "cs", "unité", "sachet"];
    $ingTypes = ["fruit", "légume", "viande", "poisson", "crustacé", "épice", "condiment", "fruit sec", "produit laitier", "féculent", "herbe", "boisson", "produit transformé", "légumineuse", "unknown"];

    foreach ($regimes as $regime) {
      $newRegime = new Regime();
      $newRegime->setLabel($regime);
      $this->em->persist($newRegime);
    }

    foreach ($types as $type) {
      $newType = new Type();
      $newType->setLabel($type);
      $this->em->persist($newType);
    }

    foreach ($units as $unit) {
      $newUnit = new Unit();
      $newUnit->setLabel($unit);
      $this->em->persist($newUnit);
    }

    foreach ($ingTypes as $ingType) {
      $newIngType = new IngredientType();
      $newIngType->setLabel($ingType);
      $this->em->persist($newIngType);
    }

    $this->em->flush();

    $io->success('Fixtures loaded');

    return 0;
  }
}
