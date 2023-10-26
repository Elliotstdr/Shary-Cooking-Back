<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UsersVoter extends Voter
{
  public function __construct(
    private UserRepository $userRepository
  ) {
  }

  protected function supports($attribute, $subject): bool
  {
    $supportsAttribute = in_array($attribute, ['OWN']);
    return $supportsAttribute;
  }

  /**
   * @param string $attribute
   * @param User $subject
   * @param TokenInterface $token
   * @return bool
   */
  protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
  {
    $user = $this->userRepository->find($subject);
    switch ($attribute) {
      case 'OWN':
        if ($user && $token->getUserIdentifier() === $user->getEmail()) {
          return true;
        }
        break;
    }
    return false;
  }
}
