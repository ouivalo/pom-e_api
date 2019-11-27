<?php


namespace App\Service;


use Mailjet\Client;
use Mailjet\Resources;
use Mailjet\Response;

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
     * @return Response
     */
    public function send( array $messages ): Response
    {

        $body = [ 'Messages' => []];

        foreach ( $messages as $message ){

            $m = $message;

            // On a defaut pour le From
            if( ! isset(  $m['From'] ) ){
                $m['From'] = ['Email' => getenv('MAILJET_FROM_EMAIL'), 'Name' => getenv('MAILJET_FROM_NAME')];
            }

            $m['TemplateLanguage'] = true;

            $body['Messages'][] = $m;


        }

        return $this->mj->post(Resources::$Email, ['body' => $body]);
    }
}