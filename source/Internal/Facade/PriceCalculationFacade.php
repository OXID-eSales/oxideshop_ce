<?php

/**
 * Created by PhpStorm.
 * User: michael
 * Date: 12.09.17
 * Time: 15:36
 */

namespace OxidEsales\EshopCommunity\Internal\Facade;

use OxidEsales\Eshop\Core\Price;
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

    public function getLegacyPrice($articleId, $userId, $shopId = 1, $amount = 1)
    {
        // get the configuration
        $viewModeIsNet = $this->context->displayNetPrices();
        $currencyRate = $this->context->getCurrencyRate();

        $simplePrice = $this->priceCalculationService->getSimplePrice($articleId, $userId, $shopId, $amount);

        /** @var Price $price */
        $price = oxNew(Price::class);
        $price->setVat($simplePrice->getVat());
        $price->setNettoMode($viewModeIsNet);

        // View mode and database mode match
        if ($viewModeIsNet == $simplePrice->isNetPrice()) {
            $price->setPrice($simplePrice->getValue() * $currencyRate);
        } else
            // View mode and database mode differ, so recalculate the price
            if ($viewModeIsNet) {
                // The database price is in pre-tax mode
                $price->setPrice($this->legacyService->calculateBruttoToNetto($simplePrice) * $currencyRate);
            } else {
                // The database is in net mode
                $price->setPrice($this->legacyService->calculateNettoToBrutto($simplePrice) * $currencyRate);
            }

        return $price;
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

}