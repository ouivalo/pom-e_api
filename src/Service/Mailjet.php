<?php


namespace App\Service;


use App\Entity\Consumer;
use Mailjet\Client;
use Mailjet\Resources;
use Mailjet\Response;

class Mailjet
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
            ['version' => 'v3']
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
                $m['From'] = ['Mailjet' => getenv('MAILJET_FROM_EMAIL'), 'Name' => getenv('MAILJET_FROM_NAME')];
            }

            $m['TemplateLanguage'] = true;

            $body['Messages'][] = $m;


        }

        return $this->mj->post(Resources::$Email, ['body' => $body], ['version' => 'v3.1']);
    }

    public function addContact( string $name, string $email ) : Response
    {


        $body = [
            'IsExcludedFromCampaigns'   => 'false',
            'Name'                      => $name,
            'Email'                     => $email
        ];

        return $this->mj->post(Resources::$Contact, ['body' => $body], ['version' => 'v3']);
    }

    public function addToList( int $contactMailjetId, array $listsId ) : Response
    {
        $contactList = [];
        foreach ( $listsId as $lId ){
            $contactList[] = [
                    'Action' => 'addnoforce',
                    'ListID' => $lId
                ];
        }
        $body = [
            'ContactsLists' => $contactList
        ];

        return $this->mj->post(Resources::$ContactManagecontactslists, ['id' => $contactMailjetId, 'body' => $body]);
    }

    public function addConsumer( Consumer $consumer ) : ?Response
    {

        $response = null;

        if( ! $consumer->getMailjetId() ){
            // On ajoute notre contact sur Mailjet
            $response = $this->addContact( $consumer->getUsername(), $consumer->getEmail() );

            if( $response->success() ){

                $contactData = $response->getData();
                $consumer->setMailjetId( $contactData[0]['ID'] );
            }
        }


        if( $consumer->getMailjetId() ){

            // On ajoute notre contact aux composteurs
            $compostersMailjetListId = [];
            foreach ( $consumer->getComposters() as $composter ){
                $mailjetListId = $composter->getMailjetListID();

                if( $mailjetListId ){
                    $compostersMailjetListId[] = $mailjetListId;
                }
            }

            if( count( $compostersMailjetListId ) > 0 ){
                $response = $this->addToList( $consumer->getMailjetId(), $compostersMailjetListId );
            }

        }

        return $response;
    }

    public function getContactContactsLists( int $contactMailjetId ) : Response
    {
        return $this->mj->get(Resources::$ContactGetcontactslists, ['id' => $contactMailjetId]);
    }
}