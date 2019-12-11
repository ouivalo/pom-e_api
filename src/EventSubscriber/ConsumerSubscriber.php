<?php


namespace App\EventSubscriber;


use ApiPlatform\Core\EventListener\EventPriorities;
use ApiPlatform\Core\Util\RequestAttributesExtractor;
use App\Entity\Consumer;
use App\Service\Mailjet;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ConsumerSubscriber implements EventSubscriberInterface
{

    private $mailjet;

    public function __construct( Mailjet $mailjet)
    {
        $this->mailjet = $mailjet;
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
            is_a($attributes['resource_class'], Consumer::class, true)
        ) {

            if (!is_iterable($controllerResult)) {
                $controllerResult = [$controllerResult];
            }

            foreach ($controllerResult as $currentObject) {

                if ($currentObject instanceof Consumer ){

                    $currentObject->setMailjetContactsLists([]);
                    $currentObject->setSubscribeToCompostriNewsletter(false);

                    if( $currentObject->getMailjetId()) {

                    $mjResponse = $this->mailjet->getContactContactsLists( $currentObject->getMailjetId() );

                    if( $mjResponse->success() ){
                        $currentObject->setMailjetContactsLists( $mjResponse->getData() );
                        $subscribeToCompostriNewsletter = false;
                        foreach ( $mjResponse->getData() as $data ){
                            if( ! $data['IsUnsub'] && $data['ListID'] === (int) getenv('MJ_COMPOSTRI_NEWSLETTER_CONTACT_LIST_ID') ){
                                $subscribeToCompostriNewsletter = true;
                            }
                        }
                        $currentObject->setSubscribeToCompostriNewsletter( $subscribeToCompostriNewsletter );
                    }
                }
                }
            }
        }
    }
}