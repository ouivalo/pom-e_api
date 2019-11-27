<?php


namespace App\Service;


use Mailjet\Client;
use Mailjet\Resources;

class Email
{

    /**
     * @var Client
     */
    private $mj;

    public function __construct()
    {
        $this->mj = new Client(
            getenv('MJ_APIKEY_PUBLIC'),
            getenv('MJ_APIKEY_PRIVATE'),
            true,
            ['version' => 'v3.1']
        );
    }


    /**
     * @param array $messages   Tableau de tableau [[ 'To' => [], 'Subject' => '', 'TemplateID' => int,  'Variables' => []]]
     * @return array
     */
    public function send( array $messages ): ? array
    {

        $body = [ 'Messages' => []];

        foreach ( $messages as $message ){

            $from = $message['From'] ?? ['Email' => getenv('MAILJET_FROM_EMAIL'), 'Name' => getenv('MAILJET_FROM_NAME')];
            $body['Messages'][] =
                [
                    'From'              => $from,
                    'To'                => $message['To'],
                    'Subject'           => $message['Subject'],
                    'TemplateID'        => $message['TemplateID'],
                    'TemplateLanguage'  => true,
                    'Variables'         => $message['Variables']
                ];

        }
        $response = $this->mj->post(Resources::$Email, ['body' => $body]);
        return $response->getData();
    }
}