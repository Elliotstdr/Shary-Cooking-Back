<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class UsersVoter extends Voter
{
  private $security = null;
  private $user = null;
  public function __construct(
    Security $security,
    private UserRepository $userRepository
  ) {
    $this->security = $security;
  }

  protected function supports($attribute, $subject): bool
  {
    $supportsAttribute = in_array($attribute, ['OWN']);
    $this->user = $this->userRepository->find($subject);

    return $supportsAttribute && $this->user;
  }

  /**
   * @param string $attribute
   * @param User $subject
   * @param TokenInterface $token
   * @return bool
   */
  protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
  {
    switch ($attribute) {
      case 'OWN':
        if ($this->user && $token->getUserIdentifier() === $this->user->getEmail()) {
          return true;
        }
        break;
    }
    return false;
  }
}
