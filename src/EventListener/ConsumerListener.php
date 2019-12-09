<?php


namespace App\EventListener;


use App\Entity\Consumer;
use App\Service\Mailjet;

class ConsumerListener
{

    /**
     * @var Mailjet
     */
    private $mj;


    /**
     * ConsumerListener constructor.
     * @param Mailjet $mj
     */
    public function __construct( Mailjet $mj )
    {
        $this->mj = $mj;

    }


    /**
     * @param Consumer $consumer
     */
    public function prePersist( Consumer $consumer ) : void
    {

        $this->mj->addConsumer( $consumer );
    }


    /**
     * @param Consumer $consumer
     */
    public function preUpdate( Consumer $consumer ) : void
    {

        $this->mj->addConsumer( $consumer );
    }
}