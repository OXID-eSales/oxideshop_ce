<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 20.07.17
 * Time: 15:32
 */

namespace OxidEsales\EshopCommunity\Internal\Utility;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Price;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\UtilsDate;
use OxidEsales\EshopCommunity\Internal\DataObject\Discount;
use OxidEsales\EshopCommunity\Internal\DataObject\SimplePrice;

/**
 * Class OxidLegacyService
 *
 * This service is a wrapper for stuff that would be
 * to much effort to refactor right now, but that needs
 * refactoring eventually. To prevent using old code
 * in the new code base, the functionality is wrapped.
 *
 * @package OxidEsales\EshopCommunity\Internal\Utilities
 */
class OxidLegacyService implements OxidLegacyServiceInterface
{
    /** @var Config  */
    private $config;
    /** @var ContextInterface */
    private $context;
    /** @var UtilsDate $dateUtility */
    private $dateUtility;

    public function __construct(Config $config, ContextInterface $context)
    {
        $this->config = $config;
        $this->context = $context;
    }

    /**
     * @return \OxidEsales\Eshop\Core\UtilsDate
     */
    private function getDateUtility() {

        if ( ! $this->dateUtility ) {
            $this->dateUtility = Registry::getUtilsDate();
        }

        return $this->dateUtility;

    }

    public function getCurrentTimeDBFormatted()
    {
        return $this->getDateUtility()->getRoundedRequestDateDBFormatted(60);

    }

    /**
     * Returns the selected address from request parameter or from session
     *
     * @return string
     */
    public function getSelectedShippingAddressId() {

        $legacyUser = $this->config->getUser();
        return $legacyUser->getSelectedAddressId();

    }

    /**
     * Initializes a price object with the correct net mode and the correct price.
     * This is obviously done so the price in the price object matches the display
     * mode (which should really go to the presentation layer, not here, but this
     * is something that is not easily refactored).
     *
     * @param double $databasePrice
     * @param double $articleVat
     * @param Discount[] $discounts
     *
     * @return Price
     */
    public function getPriceObject($databasePrice, $articleVat)
    {
        // get the configuration
        $viewModeIsNet = $this->context->displayNetPrices();
        $databasePriceIsNet = $this->context->dbPricesAreNetPrices();
        $currencyRate = $this->context->getCurrencyRate();

        /** @var Price $price */
        $price = oxNew(Price::class);
        $price->setVat($articleVat);
        $price->setNettoMode($viewModeIsNet);

        // View mode and database mode match
        if ($viewModeIsNet == $databasePriceIsNet) {
            $price->setPrice($databasePrice * $currencyRate);
        }
        else
        // View mode and database mode differ, so recalculate the price
        if ($viewModeIsNet) {
            // The database price is in pre-tax mode
            $price->setPrice(Price::brutto2Netto($databasePrice, $articleVat) * $currencyRate);
        }
        else {
            // The database is in net mode
            $price->setPrice(Price::netto2Brutto($databasePrice, $articleVat) * $currencyRate);
        }

        return $price;

    }

    public function calculateBruttoToNetto(SimplePrice $simplePrice)
    {

        return Price::brutto2Netto($simplePrice->getValue(), $simplePrice->getVat());
    }

    public function calculateNettoToBrutto(SimplePrice $simplePrice)
    {

        return Price::netto2Brutto($simplePrice->getValue(), $simplePrice->getVat());

    }
}
