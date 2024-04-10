<?php

namespace App\Controller\CustomControllers;

use App\Entity\IngredientType;
use App\Repository\IngredientTypeRepository;
use App\Repository\RegimeRepository;
use App\Repository\TypeRepository;
use App\Repository\UnitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/api/retrieveStaticData', name: 'retrieve_static_data', methods: ['GET'])]
class RetrieveStaticDataController extends AbstractController
{
  public function __construct(
    private readonly RegimeRepository $regimeRepository,
    private readonly TypeRepository $typeRepository,
    private readonly IngredientTypeRepository $ingredientTypeRepository,
    private readonly UnitRepository $unitRepository,
    private readonly NormalizerInterface $normalizerInterface
  ) {
  }

  public function __invoke()
  {
    return new JsonResponse([
      'regimes' => $this->normalizerInterface->normalize(
        $this->regimeRepository->findAll(),
      ),
      'types' => $this->normalizerInterface->normalize(
        $this->typeRepository->findAll(),
      ),
      'ingTypes' => $this->normalizerInterface->normalize(
        $this->ingredientTypeRepository->findAll(),
        IngredientType::class . "[]",
        ['groups' => ['ingtype:read']]

      ),
      'units' => $this->normalizerInterface->normalize(
        $this->unitRepository->findAll(),
      )
    ]);
  }
}
