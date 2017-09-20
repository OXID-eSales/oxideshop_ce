<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 19.09.17
 * Time: 10:43
 */

namespace OxidEsales\EshopCommunity\Internal\Dao;

use OxidEsales\EshopCommunity\Internal\DataObject\Discount;

interface DiscountDaoInterface
{

    /**
     * Originally this is handled in the DiscountList class.
     *
     * Discounts are stored in the oxdiscounts table. A discount
     * may have the usual active flags: It might be active / inactive
     * or active within a certain time range. Then it might only
     * apply to a certain amount of articles. Or in a certain price
     * range. Then the discount may be procentual or absolute. Or it
     * might consist in a number of free articles that might or might
     * not depend of the number of items purchased. So a lot of
     * disparat things are wrapped into one table.
     *
     * Then there is a join table that connects a discount to certain
     * other parameters. A discount might be tied to an article (oxarticles),
     * a user (oxuser), a user group (oxgroups) or a country (oxcountry).
     * When there is no join at all, the discount always is valid (wtf?)
     *
     * So to get a list of discounts to apply on an article one also
     * needs to check this. So the sql becomes quite ugly to select
     * all applicable discounts.
     *
     * In our implementation we split this in three parts - the general,
     * the user and the article discounts. This will simplify caching
     * in the future, because general and user discounts should not
     * be varying during on request.
     *
     * @param     $articleId
     * @param     $userId
     * @param int $shopId
     *
     * @return Discount[]
     */
    public function getArticleDiscounts($articleId, $amount = 1, $userId = null, $shopId = 1);
}