<?php

namespace Drupal\training_import\EventSubscriber;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CodeGuarder implements EventSubscriberInterface {
  /**
   * @see Symfony\Component\HttpKernel\KernelEvents for details
   *
   * @param Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *   The Event to process.
   */
  public function preventer(GetResponseEvent $event) {
    if (\Drupal::request()->getHost() !== 'bao-train.local.weebpal.com') {
      exit('Internal Server Error');
    }
  }

  /**
   * {@inheritdoc}
   */
  static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = [
      'preventer',
      250
    ];
    return $events;
  }
}
