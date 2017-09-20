<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 04.09.17
 * Time: 10:27
 */

namespace OxidEsales\EshopCommunity\Internal\DataObject;


class Discount
{

    /** @var  array $data */
    private $data;

    public function __construct($data)
    {

        $this->data = $data;
    }

    public function getId()
    {
        return $this->data['OXID'];
    }

    public function getType()
    {
        return $this->data['OXADDSUMTYPE'];
    }

    public function getBaseValue()
    {
        return $this->data['OXADDSUM'];
    }

    public function getSortIdx()
    {
        return $this->data['OXSORT'];
    }

    public function getMaxAmount()
    {

        return $this->data['OXAMOUNTTO'];
    }

    public function getMaxPrice()
    {

        return $this->data['OXPRICETO'];
    }

    public function calculateDiscountValue($currencyRate)
    {

        if ($this->getType() == 'abs') {
            return $this->getBaseValue() * $currencyRate;
        }

        if ($this->getType() == '%') {
            return $this->getBaseValue();
        }

        return 0.0;
    }


}