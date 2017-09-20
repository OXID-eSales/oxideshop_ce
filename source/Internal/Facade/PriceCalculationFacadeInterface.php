<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 13.09.17
 * Time: 09:05
 */

namespace OxidEsales\EshopCommunity\Internal\Facade;

interface PriceCalculationFacadeInterface
{

    public function getLegacyPrice($articleId, $userId, $shopId = 1, $amount = 1);
}