<?php
namespace OxidEsales\Eshop\Core\Event;

class OrderSend extends AbstractEvent
{
    const NAME = 'OrderSend';
    
    private $order;
    public function __construct($order)
    {
        $this->order = $order;
    }

    public function getOrder()
    {
        return $this->order;
    }
}
