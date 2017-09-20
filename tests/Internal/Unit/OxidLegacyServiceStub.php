<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 25.07.17
 * Time: 10:22
 */

namespace OxidEsales\EshopCommunity\Tests\Internal\Unit;


use OxidEsales\Eshop\Core\Price;
use OxidEsales\EshopCommunity\Internal\DataObject\SimplePrice;
use OxidEsales\EshopCommunity\Internal\Utilities\OxidLegacyServiceInterface;

class OxidLegacyServiceStub implements OxidLegacyServiceInterface
{

    /** @var string */
    private $currentTimeDBFormatted = '2017-07-24 10:00:00';
    /** @var Price */
    private $price = null;
    /** @var  string */
    private $selectedShippingAddressId = '';

    public function getCurrentTimeDBFormatted()
    {
        return $this->currentTimeDBFormatted;
    }

    public function setCurrentTimeDBFormatted($timeString)
    {
        $this->currentTimeDBFormatted = $timeString;
    }

    /** @return Price */
    public function getPriceObject($databasePrice, $articleVat)
    {
        if ($this->price == null) {
            throw new \Exception('You have to set the price property on test setup');
        }

        return $this->price;
    }

    public function setPriceObject(Price $price)
    {
        $this->price = $price;
    }

    /**
     * Returns the selected address from request parameter or from session
     *
     * @return string
     */
    public function getSelectedShippingAddressId()
    {
        return $this->selectedShippingAddressId;
    }

    /**
     * @param string $selectedShippingAddressId
     */
    public function setSelectedShippingAddressId($selectedShippingAddressId)
    {
        $this->selectedShippingAddressId = $selectedShippingAddressId;
    }

    public function calculateNettoToBrutto(SimplePrice $simplePrice)
    {
        return Price::netto2Brutto($simplePrice->getValue(), $simplePrice->getVat());
    }

    public function calculateBruttoToNetto(SimplePrice $simplePrice)
    {
        return Price::brutto2Netto($simplePrice->getValue(), $simplePrice->getVat());
    }
}