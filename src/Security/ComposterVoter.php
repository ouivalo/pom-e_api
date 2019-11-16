<?php
// src/Security/PostVoter.php
namespace App\Security;

use App\Entity\Composter;
use App\Entity\Permanence;
use App\Repository\ComposterRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Doctrine\ORM\EntityManagerInterface;

class ComposterVoter extends Voter
{
    // these strings are just invented: you can use anything
    const OPENER = 'Opener';
    const REFERENT = 'Referent';
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var ComposterRepository
     */
    private $composterRepository;


    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager )
    {
        $this->requestStack = $requestStack;
        $this->composterRepository = $entityManager->getRepository(Composter::class);
    }

    protected function supports($attribute, $subject) : bool
    {

        $supports = in_array($attribute, [self::OPENER, self::REFERENT], true) &&
            ( $subject instanceof Composter || $subject instanceof Permanence);

        if( ! $supports ) {
            // Si $subject est null on est peut être sur une création
            $currentRequest = $this->requestStack->getCurrentRequest();
            $supports = $subject === null && $currentRequest instanceof Request && $currentRequest->getMethod() === 'POST';
        }
        return $supports;

    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token) : bool
    {
        $grant = false;

        $roles = $token->getRoleNames();
        $user = $token->getUser();
        if ( in_array( 'ROLE_ADMIN', $roles, true )) {
          return true;
        }

        // $subject is a Composter or Permanence
        if( $subject instanceof Composter ){
            $composter = $subject;
        } elseif ( $subject instanceof Permanence ){
            $composter = $subject->getComposter();
        } else {
            // On est sur la création d'une entité
            $currentRequest = $this->requestStack->getCurrentRequest();
            if( $currentRequest && $currentRequest->getPathInfo() === '/permanences'){
                // On tente de créer une permanence
                $request_body = json_decode($currentRequest->getContent(), false);
                $composter_string = $request_body->composter;
                $composter_url_array = explode( '/', $composter_string);
                $composter_slug = end($composter_url_array );
                $composter = $this->composterRepository->findOneBy(['slug' => $composter_slug]);
            } else {
                return false;
            }
        }

        if( ! $composter instanceof Composter ){
            return false;
        }

        foreach ($user->getUserComposters() as $userComposter) {
          if ( $userComposter->getCapability() === $attribute  &&
            $userComposter->getComposter()->getId() === $composter->getId() ) {
            $grant = true;
          }
        }
        return $grant;
    }
}
