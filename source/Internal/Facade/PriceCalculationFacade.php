<?php

/**
 * Created by PhpStorm.
 * User: michael
 * Date: 12.09.17
 * Time: 15:36
 */

namespace OxidEsales\EshopCommunity\Internal\Facade;

use OxidEsales\Eshop\Core\Price;
use OxidEsales\EshopCommunity\Application\Model\Basket;
use OxidEsales\EshopCommunity\Internal\DataObject\SimplePrice;
use OxidEsales\EshopCommunity\Internal\Service\PriceCalculationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Utilities\ContextInterface;
use OxidEsales\EshopCommunity\Internal\Utilities\OxidLegacyServiceInterface;

class PriceCalculationFacade implements PriceCalculationFacadeInterface
{

    /**
     * @var PriceCalculationServiceInterface $priceCalculationService
     */
    private $priceCalculationService;

    /**
     * @var ContextInterface $context
     */
    private $context;

    /**
     * @var OxidLegacyServiceInterface $legacyService
     */
    private $legacyService;

    public function __construct(ContextInterface $context, OxidLegacyServiceInterface $legacyService,
                                PriceCalculationServiceInterface $priceCalculationService)
    {
        $this->context = $context;
        $this->legacyService = $legacyService;
        $this->priceCalculationService = $priceCalculationService;
    }

    /**
     * This is still, after refactoring, a terrible method, because so many
     * sick parameters influence the price calculation. And the result object,
     * the legacy price object, is hardly understandable - due to mixing
     * view logic with calculation logic. This can't be disentangled unless
     * the legacy price object is completely discarded.
     *
     * @param       $articleId
     * @param       $userId
     * @param int   $shopId
     * @param int   $amount
     * @param array $selectList
     * @param null  $viewPricesAreNetPrices
     *
     * @return Price
     */
    public function getLegacyPrice($articleId, $userId, $shopId = 1, $amount = 1, $selectList = [], $viewPricesAreNetPrices = null)
    {
        $simplePrice = $this->getSimplePrice($articleId, $userId, $shopId, $amount);
        $simplePrice = $this->applySelectListModifications($articleId, $simplePrice, $selectList);

        // How do we want the price to be displayed by the consumer of this method?
        if ($viewPricesAreNetPrices !== null) {
            $viewModeIsNet = $viewPricesAreNetPrices;
        } else {
            $viewModeIsNet = $this->context->displayNetPrices();
        }
        $viewModePrice = $this->calculateViewModePrice($viewModeIsNet, $simplePrice);

        /** @var Price $price */
        $price = oxNew(Price::class);
        if ($simplePrice->isUserVatTaxable()) {
            $price->setVat($simplePrice->getVat());
        }
        else {
            $price->setVat(0.0);
        }
        $price->setNettoMode($viewModeIsNet);
        $price->setPrice($viewModePrice);
        $price->multiply($this->context->getCurrencyRate());

        return $price;
    }

    private function calculateViewModePrice($viewModeIsNet, SimplePrice $simplePrice) {

        // This calculation logic is really, really sick - it depends on three parameters
        // - if the view mode is netto or brutto
        // - if the database mode is netto or brutto
        // - if the user needs to pay VAT

        // So the cases are (regardless if they makes sense or not):
        //
        // view mode | database mode | user taxable | calculation method
        // netto     | netto         | true         | None
        // netto     | netto         | false        | None
        // netto     | brutto        | true         | brutto -> netto
        // netto     | brutto        | false        | brutto -> netto
        // brutto    | netto         | true         | netto -> brutto
        // brutto    | netto         | false        | None
        // brutto    | brutto        | true         | None
        // brutto    | brutto        | false        | brutto -> netto

        if ($viewModeIsNet) {
            if ($simplePrice->isNetValue()) {
                return $simplePrice->getValue();
            }
            else {
                return $this->legacyService->calculateBruttoToNetto($simplePrice);
            }
        }
        else {
            if ($simplePrice->isNetValue()) {
                if ($simplePrice->isUserVatTaxable()) {
                    return $this->legacyService->calculateNettoToBrutto($simplePrice);
                }
                else {
                    return $simplePrice->getValue();
                }
            }
            else {
                if ($simplePrice->isUserVatTaxable()) {
                    return $simplePrice->getValue();
                }
                else {
                    return $this->legacyService->calculateBruttoToNetto($simplePrice);
                }
            }
        }
    }

    public function getSimplePrice($articleId, $userId, $shopId, $amount)
    {

        if (!$this->context->loadPriceInformation()) {
            return new SimplePrice(0, 0, true, false, $articleId, $userId, $shopId, $amount);
        }

        $price = $this->priceCalculationService->getSimplePrice($articleId, $userId, $shopId, $amount);

        return $price;
    }

    public function getBasketPrice($articleId, $userId, $shopId, $amount, $selections)
    {

        $legacyPrice = $this->getLegacyPrice($articleId, $userId, $shopId, $amount, $selections);

        return $legacyPrice;
    }

    /**
     * @param Price  $price
     * @param string $articleId
     * @param string $userId
     * @param int    $shopId
     *
     * @return Price
     */
    public function applyDiscounts($price, $articleId, $amount, $userId, $shopId)
    {

        $discounts = $this->priceCalculationService->getArticleDiscounts($articleId, $amount, $userId, $shopId);

        foreach ($discounts as $discount) {

            $discountValue = $discount->calculateDiscountValue($this->context->getCurrencyRate());
            if ($discountValue != 0.0) {
                $price->setDiscount($discountValue, $discount->getType());
            }
        }
        $price->calculateDiscount();

        return $price;
    }

    /**
     * @param SimplePrice $price
     * @param string      $articleId
     * @param array       $selections
     *
     * @return SimplePrice
     */
    private function applySelectListModifications($articleId, $price, $selections)
    {

        if ($selections == null || sizeof($selections) == 0) {
            // No need to access the database
            return $price;
        }

        $selectList = $this->priceCalculationService->getSelectList($articleId);

        $modifiedPrice = $selectList->modifyPriceForSelection($price->getValue(), $selections);

        return new SimplePrice($modifiedPrice, $price->getVat(), $price->isUserVatTaxable(), $price->isNetValue(),
            $price->getArticleId(), $price->getUserId(), $price->getShopId(), $price->getAmount());
    }

}