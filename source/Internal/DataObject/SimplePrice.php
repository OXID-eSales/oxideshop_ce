<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 12.09.17
 * Time: 15:39
 */

namespace OxidEsales\EshopCommunity\Internal\DataObject;


/**
 * Class SimpleViewPrice
 *
 * This class represents a price. There is no calculation
 * done on this class at all. It just holds the database
 * value (although this may be altering depending on the
 * user and the amount bought), the VAT that needs to be
 * applied and if the database value is in net or pre-tax
 * mode.
 *
 * For reference, the userId and the amount are also contained
 * in this object. This is more to inid
 *
 * @package OxidEsales\EshopCommunity\Internal\DataObject
 */
class SimplePrice
{

    /** @var  double $price */
    private $value;

    /** @var  double $vat */
    private $vat;

    /** @var  bool $userIsVatTaxable */
    private $userIsVatTaxable;

    /** @var boolean $isNetValue */
    private $isNetValue;

    /** @var string $articleId */
    private $articleId;

    /** @var  string $userId */
    private $userId;

    /** @var  int $shopId */
    private $shopId;

    /** @var  int amount */
    private $amount;

    /**
     * SimplePrice constructor.
     *
     * @param double  $value
     * @param double  $vat
     * @param boolean $isNetPrice
     */
    public function __construct($value, $vat, $userIsVatTaxable, $isNetPrice, $articleId, $userId, $shopId, $amount)
    {
        $this->value = $value;
        $this->vat = $vat;
        $this->userIsVatTaxable = $userIsVatTaxable;
        $this->isNetValue = $isNetPrice;
        $this->articleId = $articleId;
        $this->userId = $userId;
        $this->shopId = $shopId;
        $this->amount = $amount;
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
     * @return bool
     */
    public function isUserVatTaxable()
    {
        return $this->userIsVatTaxable;
    }

    /**
     * @return boolean
     */
    public function isNetValue()
    {
        return $this->isNetValue;
    }

    /**
     * @return string
     */
    public function getArticleId()
    {
        return $this->articleId;
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    public function getShopId()
    {
        return $this->shopId;
    }
    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

}