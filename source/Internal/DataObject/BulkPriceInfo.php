<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 16.08.17
 * Time: 15:04
 */

namespace OxidEsales\EshopCommunity\Internal\DataObject;


class BulkPriceInfo
{

    private $absoluteValue = null;
    private $discount = 0.0;

    public function __construct($queryResult)
    {

        if ($queryResult) {
            $this->absoluteValue = $queryResult['oxaddabs'];
            $this->discount = $queryResult['oxaddperc'];
        }
    }

    public function isAbsoluteValue()
    {
        return $this->absoluteValue != null and $this->absoluteValue != 0.0;
    }

    public function getAbsoluteValue()
    {
        return $this->absoluteValue;
    }

    public function isDiscount()
    {
        return $this->discount != 0.0;
    }

    public function calculateBulkPrice($basePrice)
    {
        if ($this->isAbsoluteValue()) {
            return $this->absoluteValue;
        }
        if ($this->isDiscount()) {
            return $basePrice * ((100.0 - $this->discount) / 100.0);
        }

        return $basePrice;
    }
}