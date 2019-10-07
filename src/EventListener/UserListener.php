<?php


namespace App\EventListener;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserListener
{

    protected $encoder;
    protected $em;


    /**
     * UserListener constructor.
     * @param UserPasswordEncoderInterface $encoder
     * @param EntityManagerInterface $entityManager
     */
    public function __construct( UserPasswordEncoderInterface $encoder, EntityManagerInterface $entityManager)
    {
        $this->encoder = $encoder;
        $this->em = $entityManager;
    }

    /**
     * @param User $user
     */
    public function prePersist(User $user): void
    {
        $this->encodePassword($user);
    }


    /**
     * @param User $user
     */
    public function preUpdate( User $user ){


        $this->encodePassword( $user );

        // necessary to force the update to see the change
        $meta = $this->em->getClassMetadata(get_class($user));
        $this->em->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $user);
    }



    /**
     * @param User $user
     */
    private function encodePassword(User $user): void
    {

        $encoded = $this->encoder->encodePassword(
            $user, $user->getPassword()
        );
        $user->setPassword($encoded);
    }
}