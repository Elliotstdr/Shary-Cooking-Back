<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UsersVoter extends Voter
{
  public function __construct(
    private readonly UserRepository $userRepository
  ) {
  }

  protected function supports($attribute, $subject): bool
  {
    $supportsAttribute = in_array($attribute, ['OWN', 'NOT_GUEST']);
    return $supportsAttribute;
  }

  /**
   * @param string $attribute
   * @param int $subject
   * @param TokenInterface $token
   * @return bool
   */
  protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
  {
    $user = $this->userRepository->find($subject);
    switch ($attribute) {
      case 'OWN':
        if ($user && $token->getUserIdentifier() === $user->getEmail()) {
          return true;
        }
        break;
      case 'NOT_GUEST':
        return $token->getUserIdentifier() !== 'test@test.com';
        break;
    }
    return false;
  }
}
