<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;

class DeleteOldFileService extends AbstractController
{
  public function __construct(
    private readonly string $hfUrl
  ) {
  }

  public function deleteOldFile(string $oldFilePath)
  {
    if (str_contains($oldFilePath, $this->hfUrl)) {
      return;
    }
    if ($oldFilePath) {
      $fileSystem = new Filesystem();
      $projectDir = $this->getParameter('kernel.project_dir');
      $fileSystem->remove($projectDir . '/public' . $oldFilePath);
    }
  }
}
