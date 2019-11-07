<?php


namespace App\EventListener;

use Mailjet\Client;
use Mailjet\Resources;
use App\Entity\ComposterContact;

class ComposterContactListener
{

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
            $user = $userC->getUser();
            $recipients[] = [
                'Email' => $user->getEmail(),
                'Name' => $user->getUsername()
            ];
        }

        $subject = "Demande de contact pour le composteur $name";

        $mj = new Client(getenv('MJ_APIKEY_PUBLIC'), getenv('MJ_APIKEY_PRIVATE'), true, ['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => getenv('MAILJET_FROM_EMAIL'),
                        'Name' => getenv('MAILJET_FROM_NAME')
                    ],

                    'To' => $recipients,
                    'Subject' => $subject,
                    'TemplateID' => 1076948,
                    'TemplateLanguage' => true,
                    'Variables' => [
                        'email' => $composterContact->getEmail(),
                        'message' => $composterContact->getMessage()
                    ]
                ]
            ]
        ];

        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $composterContact->setSentByMailjet($response->success());
    }
}
