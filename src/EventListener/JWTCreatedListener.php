<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;


class JWTCreatedListener
{

  /**
   * @var RequestStack
   */
  private $requestStack;


  /**
   * @param RequestStack $requestStack
   */
  public function __construct(RequestStack $requestStack)
  {
    $this->requestStack = $requestStack;
  }

  /**
   * @param JWTCreatedEvent $event
   *
   * @return void
   */
  public function onJWTCreated(JWTCreatedEvent $event)
  {
    $user = $event->getUser();
    $payload = $event->getData();
    $composters = [];

    foreach ($user->getUserComposters() as $comp) {
      $composters[] = [
          'slug'        => $comp->getComposter()->getSlug(),
          'name'        => $comp->getComposter()->getName(),
          'capability'  => $comp->capability()
      ];
    }
    $payload['composters'] = $composters;
    $payload['username'] = $user->getUsername();
    $payload['userId'] = $user->getId();

    $event->setData($payload);

  }
}
