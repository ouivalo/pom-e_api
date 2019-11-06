<?php
// src/Security/PostVoter.php
namespace App\Security;

use App\Entity\Composter;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ComposterVoter extends Voter
{
  // these strings are just invented: you can use anything
  const OPENER = 'Opener';
  const REFERENT = 'Referent';

  protected function supports($attribute, $subject)
  {
    // if the attribute isn't one we support, return false
    if (!in_array($attribute, [self::OPENER, self::REFERENT])) {
      return false;
    }

    // only vote on Post objects inside this voter
    if (!$subject instanceof Composter) {
      return false;
    }

    return true;
  }

  protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
  {
    $user = $token->getUser();

    if (!$user instanceof User) {
      // the user must be logged in; if not, deny access
      return false;
    }

    if (in_array('ROLE_ADMIN', $user->getRoles())) {
      return true;
    }



    // you know $subject is a Composter object, thanks to supports
    /** @var Composter $composter */
    $composter = $subject;
    $grant = false;
    foreach ($user->getUserComposters() as $comp) {
      if ($comp->getComposter()->getId() === $composter->getId() && $comp->getCapability() === $attribute) {
        $grant = true;
      }
    }
    return $grant;
  }
}
