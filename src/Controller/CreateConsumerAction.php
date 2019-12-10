<?php


namespace App\Controller;


use App\Entity\Consumer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CreateConsumerAction extends AbstractController
{

    public function __invoke( Consumer $data ): Consumer
    {

        // On vérifie qu'on est pas en train d'ajouter un cosumer qui existe déja
        $email = $data->getEmail();

        $consumer =  $this->getDoctrine()
            ->getRepository(Consumer::class)
            ->findOneBy( [ 'email' => $email ]);

        if( $consumer instanceof Consumer ){
            $composters = $data->getComposters();
            $username = $data->getUsername();
            $subscribeToCompostriNewsletter = $data->getSubscribeToCompostriNewsletter();

            $data = $consumer;
            foreach ( $composters as $c ){
                $data->addComposter( $c );
            }
            $data->setUsername( $username );
            $data->setSubscribeToCompostriNewsletter( $subscribeToCompostriNewsletter );
        }

        return $data;
    }
}