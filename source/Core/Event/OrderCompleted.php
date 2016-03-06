<?php
namespace OxidEsales\Eshop\Core\Event;

// temporary extension
use Symfony\Component\EventDispatcher\Event;

class OrderCompleted extends Event
{
    // temporary!
    private $order;

    public function __construct($order, $user, $basket, $payment)
    {
        $this->order   = $order;

        // hack!!!
        $order->_oUser    = $user;
        $order->_oBasket  = $basket;
        $order->_oPayment = $payment;
    }

    public function getOrder()
    {
        return $this->order;
    }
}