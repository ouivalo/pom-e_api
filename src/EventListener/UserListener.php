<?php


namespace App\EventListener;


use App\Entity\User;
use App\Service\Email;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class UserListener
{

    protected $encoder;
    protected $em;
    protected $tokenGenerator;
    protected $email;


    /**
     * UserListener constructor.
     * @param UserPasswordEncoderInterface $encoder
     * @param EntityManagerInterface $entityManager
     * @param TokenGeneratorInterface $tokenGenerator
     * @param Email $email
     */
    public function __construct( UserPasswordEncoderInterface $encoder, EntityManagerInterface $entityManager, TokenGeneratorInterface $tokenGenerator, Email $email)
    {
        $this->encoder          = $encoder;
        $this->em               = $entityManager;
        $this->tokenGenerator   = $tokenGenerator;
        $this->email            = $email;
    }

    /**
     * @param User $user
     */
    public function prePersist(User $user): void
    {
        $this->encodePassword($user);

        /**
         * Pour les utilisateur nouvellement créer qui sont en enabled = false :
         *  1. On crée un token
         *  2. On envoie un mail pour qu'il puisse confirmer leur compte
         */
        if( ! $user->getEnabled() ){

            $userConfirmedAccountURL =  $user->getUserConfirmedAccountURL();
            if( $userConfirmedAccountURL ){

                $resetToken = $this->tokenGenerator->generateToken();
                $user->setResetToken( $resetToken );

                $this->email->send([
                    [
                        'To'            => [['Email' => $user->getEmail() , 'Name' => $user->getUsername() ]],
                        'Subject'       => '[Compostri] Confirmer votre compte',
                        'TemplateID'    => (int) getenv('MJ_VERIFIED_ACCOUNT_TEMPLATE_ID'),
                        'Variables'     => [ 'recovery_password_url' => "{$userConfirmedAccountURL}?token={$resetToken}"]
                    ]
                ]);
            } else {
                throw new BadRequestHttpException('"userConfirmedAccountURL" champs obligatoire pour la création d‘utilisateur');
            }
        }
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

        if (!$user->getPlainPassword()) {
            return;
        }

        $encoded = $this->encoder->encodePassword(
            $user, $user->getPlainPassword()
        );

        $user->setPassword($encoded);
    }
}