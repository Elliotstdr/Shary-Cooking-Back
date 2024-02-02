<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;

class DeleteOldFileService extends AbstractController
{
  public function __construct()
  {
  }

  public function deleteOldFile($oldFilePath)
  {
    if ($oldFilePath) {
      $fileSystem = new Filesystem();
      $projectDir = $this->getParameter('kernel.project_dir');
      $fileSystem->remove($projectDir . '/public' . $oldFilePath);
    }
  }
}
