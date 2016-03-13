<?php
namespace OxidEsales\Eshop\Core\Event;

class PriceAlarmCreated extends AbstractEvent
{
    const NAME = 'PriceAlarmCreated';

    private $params;
    private $priceAlarm;
    public function __construct($parameters, $priceAlarm)
    {
        $this->params     = $parameters;
        $this->priceAlarm = $priceAlarm;
    }

    public function getParameters()
    {
        return $this->params;
    }

    public function getPriceAlarm()
    {
        return $this->priceAlarm;
    }
}