<?php

namespace App\Controller\Recipe;

use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Intervention\Image\ImageManagerStatic as Image;

class PostImageRecipe extends AbstractController
{
  public function __construct()
  {
  }

  public function __invoke(Request $request, int $id, RecipeRepository $rr, EntityManagerInterface $em): JsonResponse
  {
    $jsonData = json_decode($request->getContent(), true);
    $base64File = $jsonData['file'];

    $recipe = $rr->find($id);

    $fileData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64File));
    $fileName = uniqid('', true) . '.jpg';
    $filePath = $this->getParameter('kernel.project_dir') . '/public/media/' . $fileName;

    $GBPicture = Image::make($fileData);
    $pictureWidth = $GBPicture->width();
    $pictureHeight = $GBPicture->height();

    $divider = $pictureWidth / 1000;

    if ($divider > 1) {
      $GBPicture->resize($pictureWidth / $divider, $pictureHeight / $divider);
      $GBPicture->save($filePath);
    } else {
      file_put_contents($filePath, $fileData);
    }

    $recipe->setImageUrl('/media/' . $fileName);
    $em->persist(($recipe));
    $em->flush();

    return new JsonResponse('File uploaded successfully');
  }
}
