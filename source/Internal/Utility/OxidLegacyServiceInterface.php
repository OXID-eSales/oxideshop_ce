<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 20.07.17
 * Time: 15:33
 */

namespace OxidEsales\EshopCommunity\Internal\Utility;


use OxidEsales\Eshop\Core\Price;
use OxidEsales\EshopCommunity\Internal\DataObject\SimplePrice;

interface OxidLegacyServiceInterface
{

    /**
     * @return string
     */
    public function getCurrentTimeDBFormatted();

    /**
     * Returns the selected address from request parameter or from session
     *
     * @return string
     */
    public function getSelectedShippingAddressId();

    public function calculateBruttoToNetto(SimplePrice $simplePrice);

    public function calculateNettoToBrutto(SimplePrice $simplePrice);

}