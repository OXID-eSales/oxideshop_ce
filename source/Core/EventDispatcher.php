<?php
namespace OxidEsales\Eshop\Core;

use OxidEsales\Eshop\Core\Event\AbstractEvent;
use OxidEsales\Eshop\Core\Event\ArticleListener;
use OxidEsales\Eshop\Core\Event\ArticleSaved;
use OxidEsales\Eshop\Core\Event\MailerListener;
use OxidEsales\Eshop\Core\Event\NewsletterSubscribed;
use OxidEsales\Eshop\Core\Event\OrderCompleted;
use OxidEsales\Eshop\Core\Event\OrderSend;
use OxidEsales\Eshop\Core\Event\PriceAlarmCreated;
use OxidEsales\Eshop\Core\Event\UserCreated;
use Symfony\Component\EventDispatcher\EventDispatcher as SymfonyEventDispatcher;

class EventDispatcher
{
    const EVENTLISTENER_SENDMAIL = 'core.mailer';
    const EVENTLISTENER_ARTICLE = 'core.article';

    private $dispatcher;

    public function __construct(SymfonyEventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;

        $mailerListener = new MailerListener(
            DiContainer::getInstance()->get(DiContainer::CONTAINER_CORE_MAILER)
        );

        $articleListener = new ArticleListener();

        $dispatcher->addListener(
            OrderCompleted::NAME,
            array(
                $mailerListener,
                'onOrderCompleted'
            )
        );

        $dispatcher->addListener(
            PriceAlarmCreated::NAME,
            array(
                $mailerListener,
                'onPriceAlarmCreated'
            )
        );

        $dispatcher->addListener(
            OrderSend::NAME,
            array(
                $mailerListener,
                'onOrderSend'
            )
        );

        $dispatcher->addListener(
            NewsletterSubscribed::NAME,
            array(
                $mailerListener,
                'onNewsletterSubscribed'
            )
        );

        $dispatcher->addListener(
            UserCreated::NAME,
            array(
                $mailerListener,
                'onUserCreated'
            )
        );

        $dispatcher->addListener(
            ArticleSaved::NAME,
            array(
                $articleListener,
                'onArticleSaved'
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
