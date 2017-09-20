<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 16.08.17
 * Time: 14:44
 */

namespace OxidEsales\EshopCommunity\Internal\Dao;

use OxidEsales\EshopCommunity\Internal\DataObject\BasicPriceInformation;
use OxidEsales\EshopCommunity\Internal\DataObject\BulkPriceInfo;
use OxidEsales\EshopCommunity\Internal\DataObject\Discount;

interface PriceInformationDaoInterface extends BaseDaoInterface
{

    /**
     * @param     $amount
     * @param     $articleId
     * @param int $shopId
     *
     * @return BulkPriceInfo
     */
    public function getBulkPriceInformation($amount, $articleId, $shopId = 1);

    /**
     * @param     $priceGroup
     * @param     $articleId
     * @param int $shopId
     *
     * @return double|null
     */
    public function getGroupPrice($priceGroup, $articleId, $shopId = 1);

    /**
     * @param     $articleId
     * @param int $shopId
     *
     * @return BasicPriceInformation
     */
    public function getBasicPriceInformation($articleId, $shopId = 1);

    /**
     * @param     $articleId
     * @param int $shopId
     *
     * @return double|null
     */
    public function getVatFromCategory($articleId, $shopId = 1);

}
