<?php
namespace OxidEsales\Eshop\Core;

use OxidEsales\Eshop\Core\Event\MailerListener;
use Symfony\Component\EventDispatcher\EventDispatcher as SymfonyEventDispatcher;

// temporary!!!
use Symfony\Component\EventDispatcher\Event;

class EventDispatcher
{
    const EVENTLISTENER_SENDMAIL = 'core.mailer';

    private static $eventDispatcher;
    public static function getInstance()
    {
        if (static::$eventDispatcher) {
            new self(new SymfonyEventDispatcher());
        }

        return static::$eventDispatcher;
    }

    private $dispatcher;
    protected function __construct(SymfonyEventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;

        $mailerListener = new MailerListener(
            new \oxEmail(DiContainer::getInstance()->get(DiContainer::CONTAINER_CORE_MAILER))
        );

        $dispatcher->addListener(
            static::EVENTLISTENER_SENDMAIL,
            array(
                $mailerListener,
                'onOrderCompleted'
            )
        );
    }

    /**
     * @param $eventName
     * @param Event|null $event
     */
    public function dispatch($eventName, Event $event = null)
    {
        $this->dispatcher->dispatch($eventName, $event);
    }
}
