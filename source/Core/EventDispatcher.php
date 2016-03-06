<?php
namespace OxidEsales\Eshop\Core;

use OxidEsales\Eshop\Core\Event\AbstractEvent;
use OxidEsales\Eshop\Core\Event\MailerListener;
use Symfony\Component\EventDispatcher\EventDispatcher as SymfonyEventDispatcher;

class EventDispatcher
{
    const EVENTLISTENER_SENDMAIL = 'core.mailer';

    private $dispatcher;

    public function __construct(SymfonyEventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;

        $mailerListener = new MailerListener(
            DiContainer::getInstance()->get(DiContainer::CONTAINER_CORE_MAILER)
        );

        $dispatcher->addListener(
            static::EVENTLISTENER_SENDMAIL,
            array(
                $mailerListener,
                'onOrderCompleted'
            )
        );

        $dispatcher->addListener(
            static::EVENTLISTENER_SENDMAIL,
            array(
                $mailerListener,
                'onPriceAlarmCreated'
            )
        );

        $dispatcher->addListener(
            static::EVENTLISTENER_SENDMAIL,
            array(
                $mailerListener,
                'onOrderSend'
            )
        );

        $dispatcher->addListener(
            static::EVENTLISTENER_SENDMAIL,
            array(
                $mailerListener,
                'onNewsletterSubscribed'
            )
        );

        $dispatcher->addListener(
            static::EVENTLISTENER_SENDMAIL,
            array(
                $mailerListener,
                'onUserCreated'
            )
        );
    }

    /**
     * @param $eventName
     * @param AbstractEvent|null $event
     */
    public function dispatch($eventName, AbstractEvent $event = null)
    {
        $this->dispatcher->dispatch($eventName, $event);
    }
}
