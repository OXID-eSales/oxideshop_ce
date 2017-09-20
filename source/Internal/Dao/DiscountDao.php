<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 19.09.17
 * Time: 10:16
 */

namespace OxidEsales\EshopCommunity\Internal\Dao;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\Expression\CompositeExpression;
use Doctrine\DBAL\Query\QueryBuilder;
use OxidEsales\EshopCommunity\Internal\DataObject\Discount;
use OxidEsales\EshopCommunity\Internal\Utilities\ContextInterface;
use OxidEsales\EshopCommunity\Internal\Utilities\OxidLegacyServiceInterface;

class DiscountDao extends BaseDao implements DiscountDaoInterface
{

    private $generalDiscountsCache = null;
    private $userDiscountsCache = [];

    /** @var PriceInformationDaoInterface $priceInformationDao */
    private $priceInformationDao;

    /** @var  UserDaoInterface $userDao */
    private $userDao;

    public function __construct(Connection $connection,
                                PriceInformationDaoInterface $priceInformationDao,
                                UserDaoInterface $userDao,
                                ContextInterface $context,
                                OxidLegacyServiceInterface $legacyService)
    {
        parent::__construct('oxdiscount', $connection, $context, $legacyService);
        $this->priceInformationDao = $priceInformationDao;
        $this->userDao = $userDao;
    }

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
    public function getArticleDiscounts($articleId, $amount = 1, $userId = null, $shopId = 1)
    {

        $discountRecords =
            array_merge(
                array_merge($this->getGeneralArticleDiscounts($articleId, $amount, $shopId), $this->getUserDiscounts($userId, $shopId)),
                $this->getItemDiscounts($articleId, $amount, $shopId)
            );


        $discounts = [];
        foreach ($discountRecords as $discountData) {
            $discounts[] = new Discount($discountData);
        }
        usort(
            $discounts, function ($d1, $d2) {
            return ($d1->getSortIdx() < $d2->getSortIdx()) ? -1 : 1;
        }
        );

        return $discounts;
    }

    /**
     * This checks all general discounts if the amount / price range matches
     *
     * @param $articleId
     * @param $amount
     * @param $shopId
     *
     * @return array
     */
    private function getGeneralArticleDiscounts($articleId, $amount, $shopId)
    {

        $generalArticleDiscounts = [];

        foreach ($this->fetchAllGeneralArticleDiscounts($shopId) as $generalDiscount) {
            if ($generalDiscount['OXAMOUNTTO'] != 0 && $generalDiscount['OXAMOUNTTO'] < $amount) {
                continue;
            }
            if ($generalDiscount['OXPRICETO'] != 0 &&
                $generalDiscount['OXPRICETO'] < $this->priceInformationDao
                    ->getBasicPriceInformation($articleId, $shopId)->getBasePrice()
            ) {
                continue;
            }
            $generalArticleDiscounts[] = $generalDiscount;
        }

        return $generalArticleDiscounts;
    }

    private function fetchAllGeneralArticleDiscounts($shopId)
    {

        if ($this->generalDiscountsCache) {
            return $this->generalDiscountsCache;
        }

        $query = $this->createQueryBuilder();
        $query->select('d.*')
            ->from('oxdiscount', 'd')
            ->leftJoin('d', 'oxobject2discount', 'j', 'd.oxid = j.oxdiscountid')
            ->where(
                $query->expr()->andX(
                    $query->expr()->isNull('j.oxid'),
                    $this->getActiveExpressionForTable($query, 'd'),
                    $this->getNotABasketDiscountExpression($query)
                )
            );

        $sth = $query->execute();
        $this->generalDiscountsCache = $sth->fetchAll();

        return $this->generalDiscountsCache;
    }

    /**
     * The implementation between articlediscounts and basketdiscounts
     * is really, really strange implemented.
     *
     * First the difference between articlediscounts and basketdiscounts:
     * Other than you might imagine, a basketdiscount is not a discount
     * that is applied to the whole basket sum. It is a discount that is
     * applied to articles, but only in the context of the basket.
     *
     * That is because there are discounts of the type: If you buy enough
     * of article A, article B and article C so the total price of all
     * articles A, B and C fall into a certain range, then the discount is
     * applied to all articles A, B and C. And there is an analogue logic
     * pertaining to the amount of articles.
     *
     * The trigger, if a discount is such a "basket discount" or if it is
     * an "article discount" are the database columns oxamount and
     * oxprice. If both are 0, then it is an article discount, if at least
     * one is not 0, then it is a "basket discount".
     *
     * A "basket discount" may be a global discount - if there are no joins
     * from oxobject2discount; and it may pertain to certain articles, if
     * there are joins.
     *
     * @param QueryBuilder $query
     *
     * @return CompositeExpression
     */
    private function getNotABasketDiscountExpression(QueryBuilder $query)
    {

        return $query->expr()->andX(
            $query->expr()->eq('d.oxamount', 0),
            $query->expr()->eq('d.oxprice', 0.0)
        );
    }

    private function getRangeDiscountExpression(QueryBuilder $query)
    {

        $priceRangeExpression = $query->expr()->orX(
            $query->expr()->eq('d.oxpriceto', 0.0),
            $query->expr()->andX(
                $query->expr()->eq('aj.oxprice', 0.0),
                $query->expr()->lte('paj.oxprice', 'd.oxpriceto')
            ),
            $query->expr()->lte('aj.oxprice', 'd.oxpriceto')
        );

        $amountRangeExpression = $query->expr()->orX(
            $query->expr()->eq('d.oxamountto', 0.0),
            $query->expr()->gte('d.oxamountto', ':amount')
        );

        return $query->expr()->andX(
            $priceRangeExpression,
            $amountRangeExpression
        );
    }

    private function getUserDiscounts($userId, $shopId)
    {

        if (!$userId) {
            return [];
        }

        if (array_key_exists($userId, $this->userDiscountsCache)) {
            return $this->userDiscountsCache[$userId];
        }

        $query = $this->createQueryBuilder();

        $this->userDiscountsCache[$userId] = $this->getSpecificDiscounts($query, $this->getUserDiscountsExpression($query, $userId, $shopId));

        return $this->userDiscountsCache[$userId];
    }

    private function getItemDiscounts($articleId, $amount, $shopId)
    {

        $query = $this->createQueryBuilder();

        return $this->getSpecificDiscounts($query, $this->getArticleDiscountsExpression($query, $articleId, $amount, $shopId));
    }

    /**
     * @param QueryBuilder $query
     * @param CompositeExpression $expression
     *
     * @return array
     */
    private function getSpecificDiscounts($query, $expression)
    {

        $query->select('d.*')
            ->from('oxdiscount', 'd')
            ->join('d', 'oxobject2discount', 'j', 'd.oxid = j.oxdiscountid');

        $activeExpression = $this->getActiveExpressionForTable($query, 'd');

        $query->where($query->expr()->andX($expression, $activeExpression));

        $sth = $query->execute();

        return $sth->fetchAll();
    }

    /**
     * @param QueryBuilder $query
     * @param string       $articleId
     * @param int          $shopId
     *
     * @return CompositeExpression
     */
    private function getArticleDiscountsExpression($query, $articleId, $amount, $shopId)
    {

        $query->setParameter(':articleid', $articleId);
        $query->setParameter(':amount', $amount);
        $query->leftJoin('j', 'oxobject2category', 'cj', 'j.oxobjectid = cj.oxcatnid');
        $query->leftJoin('j', 'oxarticles', 'aj', 'j.oxobjectid = aj.oxid');
        $query->leftJoin('j', 'oxarticles', 'paj', 'j.oxobjectid = paj.oxparentid');

        $idExpression = $query->expr()->orX(
            $query->expr()->andX(
                $query->expr()->eq('j.oxobjectid', ':articleid'),
                $query->expr()->eq('j.oxtype', '\'oxarticles\'')
            ),
            $query->expr()->andX(
                $query->expr()->eq('paj.oxid', ':articleid'),
                $query->expr()->eq('j.oxtype', '\'oxarticles\'')
            ),
            $query->expr()->andX(
                $query->expr()->eq('cj.oxobjectid', ':articleid'),
                $query->expr()->eq('j.oxtype', '\'oxcategories\'')
            )
        );

        return $query->expr()->andX(
            $idExpression,
            $this->getNotABasketDiscountExpression($query),
            $this->getRangeDiscountExpression($query)
        );
    }

    /**
     * @param QueryBuilder $query
     *
     * @return CompositeExpression
     */
    private function getUserDiscountsExpression($query, $userId, $shopid)
    {

        $userExpression = $query->expr()->andX(
            $query->expr()->eq('j.oxobjectid', ':userid'),
            $query->expr()->eq('j.oxtype', '\'oxuser\'')
        );

        $countryId = $this->userDao->getUserCountryId($userId);

        $countryExpression = $query->expr()->andX(
            $query->expr()->eq('j.oxobjectid', ':countryid'),
            $query->expr()->eq('j.oxtype', '\'oxcountry\'')
        );

        $query->leftJoin('j', 'oxobject2group', 'gj', 'j.oxobjectid = gj.oxgroupsid');
        $groupExpression = $query->expr()->andX(
            $query->expr()->eq('gj.oxobjectid', ':userid'),
            $query->expr()->eq('j.oxtype', '\'oxgroups\'')
        );

        $query->setParameter(':userid', $userId);
        $query->setParameter(':countryid', $countryId);

        return $query->expr()->orX($userExpression, $countryExpression, $groupExpression);
    }

}