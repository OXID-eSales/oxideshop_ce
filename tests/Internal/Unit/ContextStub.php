<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 25.07.17
 * Time: 10:16
 */

namespace OxidEsales\EshopCommunity\Tests\Internal\Unit;


use OxidEsales\EshopCommunity\Internal\DataObject\User;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;

class ContextStub implements ContextInterface
{

    private $currentLanguageAbbreviation = "de";
    private $currentLanguageId = 0;
    private $currencyRate = 1.0;
    private $shopId = 1;
    private $useTimeCheck = false;
    private $useStock = true;
    private $parentVariantBuyable = false;
    private $overrideZeroBulkPrices = true;
    private $defaultVat = 19.0;
    private $shopUsesCategoryVat = false;
    private $displayNetPrices = true;
    private $dbPricesAreNetPrices = true;
    private $loadPriceInformation = true;
    private $useShippingAddressForVatCountry = false;
    private $homeCountryIds = [1];

    private $requestParameters = [];

    /** @var User $user */
    private $user = null;


    /** @return string */
    public function getCurrentLanguageAbbrevitation()
    {
        return $this->currentLanguageAbbreviation;
    }


    public function setCurrentLanguageAbbrevitation($languageAbbrevitation)
    {
        $this->currentLanguageAbbreviation = $languageAbbrevitation;
    }

    public function getCurrencyRate()
    {
        return $this->currencyRate;
    }

    public function setCurrencyRate($currencyRate)
    {
        $this->currencyRate = $currencyRate;
    }

    /** @return int */
    public function getCurrentLanguageId()
    {
        return $this->currentLanguageId;
    }

    public function setCurrentLanguageId($languageId)
    {
        $this->currentLanguageId = $languageId;
    }

    /** @return int */
    public function getShopId()
    {
        return $this->shopId;
    }

    public function setShopId($shopId)
    {
        $this->shopId = $shopId;
    }

    /** @return bool */
    public function useTimeCheck()
    {
        return $this->useTimeCheck;
    }

    public function setUseTimeCheck($useTimeCheck)
    {
        $this->useTimeCheck = $useTimeCheck;
    }

    /** @return bool */
    public function useStock()
    {
        return $this->useStock;
    }

    public function setUseStock($useStock)
    {

        $this->useStock = $useStock;
    }

    /** @return bool */
    public function isVariantParentBuyable()
    {
        return $this->parentVariantBuyable;
    }

    public function setVariantParentBuyable($parentVariantBuyable)
    {

        $this->parentVariantBuyable = $parentVariantBuyable;
    }

    /** @return bool */
    public function overrideZeroGroupPrices()
    {
        return $this->overrideZeroBulkPrices;
    }

    public function setOverrideZeroBulkPrices($overrideZeroBulkPrices)
    {
        $this->overrideZeroBulkPrices = $overrideZeroBulkPrices;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function resetUser()
    {
        $this->user = null;
    }

    /** @return bool */
    public function getDefaultVat()
    {
        return $this->defaultVat;
    }

    /**
     * @param double $defaultVat
     */
    public function setDefaultVat($defaultVat)
    {
        $this->defaultVat = $defaultVat;
    }

    /** @return bool */
    public function shopUsesCategoryVat()
    {
        return $this->shopUsesCategoryVat;
    }

    /**
     * @param bool $shopUsesCategoryVat
     */
    public function setShopUsesCategoryVat($shopUsesCategoryVat)
    {
        $this->shopUsesCategoryVat = $shopUsesCategoryVat;
    }

    /** @return bool */
    public function displayNetPrices()
    {
        return $this->displayNetPrices;
    }

    public function setDisplayNetPrices($displayNetPrices)
    {
        $this->displayNetPrices = $displayNetPrices;
    }

    /** @return bool */
    public function dbPricesAreNetPrices()
    {
        return $this->dbPricesAreNetPrices;
    }

    public function setDbPricesAreNetPrices($dbPricesAreNetPrices)
    {
        $this->dbPricesAreNetPrices = $dbPricesAreNetPrices;
    }

    /**
     * @return bool
     */
    public function loadPriceInformation()
    {
        return $this->loadPriceInformation;
    }

    /**
     * @param $loadPriceInformation
     */
    public function setLoadPriceInformation($loadPriceInformation) {
        $this->loadPriceInformation = $loadPriceInformation;
    }

    /** @return bool */
    public function useShippingAddressForVatCountry()
    {
        return $this->useShippingAddressForVatCountry;
    }

    public function setUseShippingAddressForVatCountry($useShippingAddressForVatCountry)
    {
        $this->useShippingAddressForVatCountry = $useShippingAddressForVatCountry;
    }

    /** @return [string] */
    public function getHomeCountryIds()
    {
        return $this->homeCountryIds;
    }

    public function setHomeCountryId($homeCountryIds)
    {
        $this->homeCountryIds = $homeCountryIds;
    }

    /** @return string */
    public function getRequestParameter($parameterName, $default = null)
    {
        if (key_exists($parameterName, $this->requestParameters)) {
            return $this->requestParameters[$parameterName];
        }

        return $default;
    }

    public function setRequestParameter($parameterName, $value)
    {
        $this->requestParameters[$parameterName] = $value;
    }
}