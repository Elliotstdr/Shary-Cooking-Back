<?php

namespace App\Controller\User;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PostImageUser extends AbstractController
{
  public function __construct()
  {
  }

  public function __invoke(Request $request, int $id, UserRepository $ur, EntityManagerInterface $em): JsonResponse
  {
    $jsonData = json_decode($request->getContent(), true);
    $base64File = $jsonData['file'];

    $user = $ur->find($id);

    $fileData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64File));
    $fileName = $jsonData['fileName'];
    file_put_contents($this->getParameter('kernel.project_dir') . '/public/media/' . $fileName, $fileData);

    $user->setImageUrl('/media/' . $fileName);
    $em->persist(($user));
    $em->flush();

    return new JsonResponse($user->getImageUrl());
  }
}
