<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Intervention\Image\ImageManagerStatic as Image;

class PostImageService extends AbstractController
{
  public function __construct(
    private readonly DeleteOldFileService $deleteOldFileService,
    private readonly string $hfUrl
  ) {
  }

  public function saveFile($file, $fileSize = 1200, $oldFilePath = null): string
  {
    if (str_contains($file, $this->hfUrl)) {
      return $file;
    }

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
      $this->deleteOldFileService->deleteOldFile($oldFilePath);
    }

    return '/media/' . $fileName;
  }
}
