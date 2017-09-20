<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 12.09.17
 * Time: 15:39
 */

namespace OxidEsales\EshopCommunity\Internal\DataObject;


class SimplePrice
{

    /** @var  double $price */
    private $value;

    /** @var  double $vat */
    private $vat;

    /** @var boolean $isNetPrice */
    private $isNetPrice;

    /**
     * SimplePrice constructor.
     *
     * @param double  $value
     * @param double  $vat
     * @param boolean $isNetPrice
     */
    public function __construct($value, $vat, $isNetPrice)
    {

        $this->value = $value;
        $this->vat = $vat;
        $this->isNetPrice = $isNetPrice;
    }

    /**
     * @return double
     */
    public function getValue()
    {

        return $this->value;
    }

    /**
     * @return double
     */
    public function getVat()
    {

        return $this->vat;
    }

    /**
     * @return boolean
     */
    public function isNetPrice()
    {

        return $this->isNetPrice;
    }
}