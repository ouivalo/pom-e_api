<?php


namespace App\EventListener;

use App\Service\Mailjet;
use Mailjet\Client;
use Mailjet\Resources;
use App\Entity\ComposterContact;

class ComposterContactListener
{

    /**
     * @var Mailjet
     */
    private $email;

    public function __construct(Mailjet $email )
    {

        $this->email = $email;
    }

    /**
     * @param ComposterContact $composterContact
     */
    public function prePersist(ComposterContact $composterContact): void
    {
        $composter = $composterContact->getComposter();
        $name = $composter->getName();
        $mc = $composter->getMc();

        $recipients = [];

        // On ajoute le maitre composteur à la liste des destinataires
        if(isset($mc)) {
            $recipients[] = [
                'Email' => $mc->getEmail(),
                'Name' => $mc->getUsername()
            ];
        }

        // Plus tous les référents qui sont ok pour être destinataires
        foreach ($composter->getUserComposters() as $userC) {

            if( $userC->getComposterContactReceiver() ){

                $user = $userC->getUser();

                $recipients[] = [
                    'Email' => $user->getEmail(),
                    'Name' => $user->getUsername()
                ];
            }

        }

        $messages = [];

        // Envoie du message a tous les destinataires
        $messages[] = [
            'ReplyTo'       => ['Email' => $composterContact->getEmail()],
            'To'            => $recipients,
            'Subject'       => "[Compostri] Demande de contact pour le composteur $name",
            'TemplateID'    => (int) getenv('MJ_CONTACT_FORM_TEMPLATE_ID' ),
            'TemplateLanguage' => true,
            'Variables'     => [
                'email'     => $composterContact->getEmail(),
                'message'   => $composterContact->getMessage()
            ]
        ];

        // Envoie d'une confirmation de message à l'expéditeur
        $messages[] = [
            'To'            => [['Email' => $composterContact->getEmail()]],
            'Subject'       => '[Compostri] Demande de contact bien envoyé',
            'TemplateID'    => (int) getenv('MJ_CONTACT_FORM_USER_CONFIRMED_TEMPLATE_ID' ),
            'TemplateLanguage' => true
        ];


        $response = $this->email->send($messages);
        $composterContact->setSentByMailjet($response->success());
    }
}
