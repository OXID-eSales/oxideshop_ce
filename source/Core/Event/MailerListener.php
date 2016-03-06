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
}
