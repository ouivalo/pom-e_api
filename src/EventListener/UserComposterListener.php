<?php


namespace App\EventListener;

use App\DBAL\Types\CapabilityEnumType;
use App\Entity\UserComposter;
use App\Service\Mailjet;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class UserComposterListener
{

    protected $email;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TokenGeneratorInterface
     */
    private $tokenGenerator;


    /**
     * UserListener constructor.
     * @param Mailjet $email
     * @param EntityManagerInterface $entityManager
     * @param TokenGeneratorInterface $tokenGenerator
     */
    public function __construct( Mailjet $email, EntityManagerInterface $entityManager, TokenGeneratorInterface $tokenGenerator)
    {
        $this->email            = $email;
        $this->entityManager    = $entityManager;
        $this->tokenGenerator   = $tokenGenerator;
    }

    public function postPersist( UserComposter $userComposter ): void
    {

        /**
         * Pour les utilisateur nouvellement rattacher a un composteur avec un statut "Ouvreur" créer qui sont en enabled = false :
         *  2. On envoie un mail pour qu'il puisse confirmer leur compte
         */
        $this->sendConfirmationMail($userComposter);

    }

    public function postUpdate( UserComposter $userComposter ){

        // Si on change les droits du l'utilisateur et qu'il n'a plus des droits ouvreur il faut désactivé l'utilisateur
        $user = $userComposter->getUser();
        $this->sendConfirmationMail($userComposter);
        if( $userComposter->getCapability() === CapabilityEnumType::USER ){
            $disabled = true;

            foreach ($user->getUserComposters() as $uc ){
                $disabled = $disabled || $uc->getCapability() === CapabilityEnumType::USER;
            }

            if($disabled){
                $user->setEnabled( false );
                $user->setResetToken( null );
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            }
        }

    }

    private function sendConfirmationMail(UserComposter $userComposter)
    {
        $user = $userComposter->getUser();
        if ($userComposter->getCapability() === CapabilityEnumType::OPENER && !$user->getEnabled() ) {

            if( ! $user->getResetToken()){
                $resetToken = $this->tokenGenerator->generateToken();
                $user->setResetToken( $resetToken );
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            }

            $userConfirmedAccountURL = $user->getUserConfirmedAccountURL();
            if ($userConfirmedAccountURL) {

                $this->email->send([
                    [
                        'To' => [['Email' => $user->getEmail(), 'Name' => $user->getUsername()]],
                        'Subject' => '[Compostri] Confirmer votre compte',
                        'TemplateID' => (int)getenv('MJ_VERIFIED_ACCOUNT_TEMPLATE_ID'),
                        'Variables' => ['recovery_password_url' => "{$userConfirmedAccountURL}?token={$user->getResetToken()}"]
                    ]
                ]);
            } else {
                throw new BadRequestHttpException('"userConfirmedAccountURL" champs obligatoire pour la création d‘utilisateur');
            }
        }
    }
}