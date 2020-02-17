<?php


namespace App\EventListener;


use App\Entity\User;
use App\Service\Mailjet;
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
     * @param Mailjet $email
     */
    public function __construct(UserPasswordEncoderInterface $encoder, EntityManagerInterface $entityManager, TokenGeneratorInterface $tokenGenerator, Mailjet $email)
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

        // On vérifie que l'utilisateur n'héxiste pas déja
        $oldUser = $this->em->getRepository( User::class )->findOneBy(['email' => $user->getEmail()] );

        if( $oldUser instanceof User ){
            throw new BadRequestHttpException('Un utilisateur possédant le même email existe déja');
        }

        /**
         * Pour les utilisateur nouvellement créer qui sont en enabled = false :
         *  1. On crée un token
         */
        if( ! $user->getEnabled() && $user->getUserConfirmedAccountURL() ){

            $resetToken = $this->tokenGenerator->generateToken();
            $user->setResetToken( $resetToken );
        }

        $this->encodePassword($user);

    }

    public function postPersist( User $user ): void
    {

        /**
         * Pour les utilisateur nouvellement créer qui sont en enabled = false :
         *  2. On envoie un mail pour qu'il puisse confirmer leur compte
         */
        if( ! $user->getEnabled() ){

            $userConfirmedAccountURL =  $user->getUserConfirmedAccountURL();
            if( $userConfirmedAccountURL ){

                $this->email->send([
                    [
                        'To'            => [['Email' => $user->getEmail() , 'Name' => $user->getUsername() ]],
                        'Subject'       => '[Compostri] Confirmer votre compte',
                        'TemplateID'    => (int) getenv('MJ_VERIFIED_ACCOUNT_TEMPLATE_ID'),
                        'Variables'     => [ 'recovery_password_url' => "{$userConfirmedAccountURL}?token={$user->getResetToken()}"]
                    ]
                ]);
            } else {
                throw new BadRequestHttpException('"userConfirmedAccountURL" champs obligatoire pour la création d‘utilisateur');
            }
        }

    }


    /**
     * @param User $user
     * @throws \Exception
     */
    public function preUpdate( User $user ) : void
    {

        if( $user->getOldPassword() && ! $this->encoder->isPasswordValid( $user, $user->getOldPassword() )){
            throw new BadRequestHttpException('L’ancien mot de passe n’ai pas le bon');
        }

        $user->setLastUpdateDate( new \DateTime() );
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