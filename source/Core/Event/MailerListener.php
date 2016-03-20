<?php
namespace OxidEsales\Eshop\Core\Event;

class MailerListener
{
    private $email;
    public function __construct(\oxEmail $email)
    {
        $this->email = $email;
    }

    public function onOrderCompleted(OrderCompleted $event)
    {
        $order = $event->getOrder();

        $this->email->sendOrderEMailToUser($order);
        $this->email->sendOrderEMailToOwner($order);
    }

    public function onOrderSend(OrderSend $event)
    {
        $order = $event->getOrder();

        //
        $this->email->sendSendedNowMail($order);

        //
        $this->email->sendStockReminder($order->_oBasket->getContents());
    }

    public function onNewsletterSubscribed(NewsletterSubscribed $event)
    {
        $this->email->sendNewsletterDbOptInMail($event->getUser());
    }

    public function onPriceAlarmCreated(PriceAlarmCreated $event)
    {
        $this->email->sendPricealarmNotification(
            $event->getParameters(),
            $event->getPriceAlarm()
        );
    }

    public function onUserCreated(UserCreated $event)
    {
        $user = $event->getUser();
        $event->sendConfirmationEmail() ?
            $this->email->sendRegisterConfirmEmail($user) :
            $this->email->sendRegisterEmail($user);
    }
}
