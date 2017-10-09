<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 19.07.17
 * Time: 10:22
 */

namespace OxidEsales\EshopCommunity\Internal\Utilities;


use OxidEsales\EshopCommunity\Internal\DataObject\User;

interface ContextInterface
{

    public function getCurrentLanguageAbbrevitation();

    public function getCurrentLanguageId();

    public function getShopId();

    /** @return  User */
    public function getUser();

    public function setUser(User $user);

    public function resetUser();

    // Wrappers arount configuration parameters

    /** @return bool */
    public function useTimeCheck();

    /** @return bool */
    public function isVariantParentBuyable();

    /** @return bool */
    public function useStock();

    /** @return bool */
    public function overrideZeroGroupPrices();

    /** @return bool */
    public function getDefaultVat();

    /** @return bool */
    public function displayNetPrices();

    /** @return bool */
    public function dbPricesAreNetPrices();

    /** @return bool */
    public function useShippingAddressForVatCountry();

    /** @return bool */
    public function loadPriceInformation();

    /** @return [string] */
    public function getHomeCountryIds();

    /** @return string */
    public function getRequestParameter($parameterName, $default = null);

    // Not really a config parameter but looks up the database

    /** @return bool */
    public function shopUsesCategoryVat();

    /** @return double */
    public function getCurrencyRate();

}
