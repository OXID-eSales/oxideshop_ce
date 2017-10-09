<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 12.09.17
 * Time: 15:50
 */

namespace OxidEsales\EshopCommunity\Internal\Service;

use OxidEsales\EshopCommunity\Internal\DataObject\Discount;
use OxidEsales\EshopCommunity\Internal\DataObject\SelectList;
use OxidEsales\EshopCommunity\Internal\DataObject\SimplePrice;
use OxidEsales\EshopCommunity\Internal\DataObject\User;

interface PriceCalculationServiceInterface
{

    /**
     * In the current implementation the base price might be derived from three different
     * sources:
     *
     * First, it might be the price stored in oxarticles.oxprice
     *
     * Now, the current user might be in a special usergroup. Then this might
     * be the usergroup price stored in oxarticles.oxprice[abc]
     *
     * And third, there might be some bulk price stored in oxprice2article, then
     * this trumps the first two ways.
     *
     * The bulk price might be twofold: Absolute or relative. When relative, the
     * discount is applied to the already established price. If absolute, it
     * overrides it.
     *
     * @param     $articleId
     * @param int $amount
     *
     * @return double
     */
    public function getRawDatabasePrice($articleId, $userId, $shopId = 1, $amount = 1);

    /**
     * This methods gets the price from the database as some sort
     * of raw value. It just contains one price - that may depend
     * on who the user is on how many items of this article are bought.
     *
     * It also determines the VAT that is or needs to be applied
     * to the raw database value. The information, if the VAT is
     * already applied or not is kept as a flag on the object.
     *
     * No calculation is done on this object - it only contains straight
     * database / config information. (There is one small exception,
     * if the discount for bulk prices is given as percentage in the
     * database, there is some calculation done)
     *
     * @param string $articleId
     * @param int    $shopId
     * @param int    $amount
     *
     * @return SimplePrice
     */
    public function getSimplePrice($articleId, $userId, $shopId = 1, $amount = 1);


    /**
     * This gets the supposed VAT for an article (regardless of the
     * user). It first gets the VAT field from the article record. If
     * it is not set, it tries the VAT for the article category. If
     * this does not yield a result either, then the default VAT for
     * the shop is returned.
     *
     * @param string $articleId
     * @param int    $shopId
     *
     * @return double
     */
    public function getArticleVat($articleId, $shopId = 1);

    /**
     * The legacy code has some really improvised functions for this. It
     * has a crude function getArticleUserVat() in the VatSelector class.
     * This might return a value or false. And the only value may be 0.
     *
     * When false is returned, the normal VAT (as determined by getArticleVat)
     * is used, otherwise a VAT of 0 is used, that means, no VAT is added.
     * This happens when the user lives within the EU and a UStId is provided
     * or the user lives outside the EU.
     *
     * We now improve this by introducing a method that determines if
     * VAT should be applied for the user or not. Then the whole getPrice()
     * mechanism may be formulated much more clearly.
     *
     * The algorithm for this method is: Determine the region of the
     * user. There are three cases: It might be the home country of the
     * shop or in the EU or outside the EU. In the home country the
     * user is taxable, outside the EU not. Within the EU it depends
     * on him having an UStId or not.
     *
     * @param User $user
     *
     * @return boolean
     */
    public function isUserVatTaxable($userId);

    /**
     * This is just a push through method to the DAO
     *
     * @param string $articleId
     * @param string $userId
     * @param int    $shopId
     *
     * @return Discount[]
     */
    public function getArticleDiscounts($articleId, $amount, $userId, $shopId);

    /**
     * @param $articleId
     *
     * @return SelectList
     */
    public function getSelectList($articleId);
}