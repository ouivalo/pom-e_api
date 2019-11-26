<?php
// api/src/EventSubscriber/ResolveMediaObjectContentUrlSubscriber.php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use ApiPlatform\Core\Util\RequestAttributesExtractor;
use App\Entity\Composter;
use App\Entity\MediaObject;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\UrlHelper;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

final class ResolveMediaObjectContentUrlSubscriber implements EventSubscriberInterface
{
    private $uploaderHelper;

    private $urlHelper;

    public function __construct(UploaderHelper $uploaderHelper, UrlHelper $urlHelper )
    {
        $this->uploaderHelper = $uploaderHelper;
        $this->urlHelper = $urlHelper;
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

                    $currentObject->contentUrl = $this->urlHelper->getAbsoluteUrl( $this->uploaderHelper->asset($currentObject, 'file') );

                } elseif ($currentObject instanceof Composter ){

                    $image = $currentObject->getImage();

                    if( $image ){
                        $image->contentUrl = $this->urlHelper->getAbsoluteUrl( $this->uploaderHelper->asset($image, 'file') );
                        $currentObject->setImage( $image );
                    }
                }

            }
        }
    }
}
