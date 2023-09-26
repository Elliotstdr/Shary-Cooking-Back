<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Intervention\Image\ImageManagerStatic as Image;
use Symfony\Component\Filesystem\Filesystem;

class PostImageService extends AbstractController
{
  public function __construct()
  {
  }

  public function saveFile($file, $fileSize = 1200, $oldFilePath = null)
  {
    $fileData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $file));
    $fileName = uniqid('', true) . '.jpg';
    $filePath = $this->getParameter('kernel.project_dir') . '/public/media/' . $fileName;

    $GBPicture = Image::make($fileData);
    $pictureWidth = $GBPicture->width();
    $pictureHeight = $GBPicture->height();

    $divider = $pictureWidth / $fileSize;

    if ($divider > 1) {
      $GBPicture->resize($pictureWidth / $divider, $pictureHeight / $divider);
      $GBPicture->save($filePath);
    } else {
      file_put_contents($filePath, $fileData);
    }

    if ($oldFilePath) {
      $this->deleteOldFile($oldFilePath);
    }

    return $fileName;
  }

  private function deleteOldFile($oldFilePath)
  {
    if ($oldFilePath) {
      $fileSystem = new Filesystem();
      $projectDir = $this->getParameter('kernel.project_dir');
      $fileSystem->remove($projectDir . '/public' . $oldFilePath);
    }
  }
}
