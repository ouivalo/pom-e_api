<?php
// api/src/EventSubscriber/ResolveMediaObjectContentUrlSubscriber.php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use ApiPlatform\Core\Util\RequestAttributesExtractor;
use App\Entity\Composter;
use App\Entity\MediaObject;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\UrlHelper;

final class ResolveMediaObjectContentUrlSubscriber implements EventSubscriberInterface
{

    private $urlHelper;
    private $params;

    public function __construct( UrlHelper $urlHelper, ContainerBagInterface $params )
    {
        $this->urlHelper = $urlHelper;
        $this->params = $params;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['onPreSerialize', EventPriorities::PRE_SERIALIZE],
        ];
    }

    public function onPreSerialize(ViewEvent $event): void
    {
        $controllerResult = $event->getControllerResult();
        $request = $event->getRequest();

        if ($controllerResult instanceof Response || !$request->attributes->getBoolean('_api_respond', true)) {
            return;
        }

        if ( ($attributes = RequestAttributesExtractor::extractAttributes($request)) &&
            ( is_a($attributes['resource_class'], MediaObject::class, true) ||
            is_a($attributes['resource_class'], Composter::class, true ) )
        ) {

            if (!is_iterable($controllerResult)) {
                $controllerResult = [$controllerResult];
            }

            foreach ($controllerResult as $currentObject) {

                if ($currentObject instanceof MediaObject) {

                    $currentObject->contentUrl = $this->getAbsoluteUrl( $currentObject->getImageName()  );

                } elseif ($currentObject instanceof Composter ){

                    $image = $currentObject->getImage();

                    if( $image ){
                        $image->contentUrl = $this->getAbsoluteUrl( $image->getImageName()  );
                        $currentObject->setImage( $image );
                    }
                }

            }
        }
    }

    private function getAbsoluteUrl( string $imageName ) : string
    {

        $dir = str_replace( $this->params->get('kernel.project_dir') . '/public', '',  $this->params->get('upload_destination') );
        return $this->urlHelper->getAbsoluteUrl( $dir . $imageName  );
    }
}
