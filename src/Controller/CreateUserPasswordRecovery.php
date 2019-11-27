<?php


namespace App\Controller;


use App\Entity\User;
use App\Entity\UserPasswordRecovery;
use App\Service\Email;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class CreateUserPasswordRecovery extends AbstractController
{

    public function __invoke(UserPasswordRecovery $data, TokenGeneratorInterface $tokenGenerator, Email $email ): UserPasswordRecovery
    {

        $em = $this->getDoctrine()->getManager();
        $user =  $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy( [ 'email' => $data->getEmail() ]);

        $newPasswordUrl = $data->getNewPasswordUrl();

        if( ! $user ){
            throw new BadRequestHttpException('Aucun utilisateur trouvé');
        }

        $resetToken = $tokenGenerator->generateToken();
        $user->setResetToken( $resetToken );
        $em->persist( $user );
        $em->flush();

        $email->send( [
            [
                'To'            => [['Email' => $user->getEmail() , 'Name' => $user->getUsername() ]],
                'Subject'       => 'Récupération de mot de passe',
                'TemplateID'    => (int) getenv('MJ_PASSWORD_RECOVERY_TEMPLATE_ID'),
                'Variables'     => [ 'recovery_password_url' => "{$newPasswordUrl}?token={$resetToken}"]
            ]
        ]);

        $data->setId( $user->getId());
        return $data;
    }
}