<?php


namespace App\EventListener;


use ApiPlatform\Core\Util\RequestAttributesExtractor;
use App\Entity\Composter;
use App\Entity\Consumer;
use App\Entity\MediaObject;
use App\Service\Mailjet;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;

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