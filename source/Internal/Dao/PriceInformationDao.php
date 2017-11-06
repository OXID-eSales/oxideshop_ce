<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 16.08.17
 * Time: 10:07
 */

namespace OxidEsales\EshopCommunity\Internal\Dao;


use Doctrine\DBAL\Connection;
use OxidEsales\EshopCommunity\Internal\DataObject\BasicPriceInformation;
use OxidEsales\EshopCommunity\Internal\DataObject\BulkPriceInfo;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Internal\Utility\OxidLegacyServiceInterface;

class PriceInformationDao extends BaseDao implements PriceInformationDaoInterface
{

    public function __construct(Connection $connection,
                                ContextInterface $context,
                                OxidLegacyServiceInterface $legacyService)
    {
        parent::__construct('oxarticles', $connection, $context, $legacyService);
    }

    /**
     * @param     $amount
     * @param     $articleId
     * @param int $shopId
     *
     * @return BulkPriceInfo
     * @throws \Exception
     */
    public function getBulkPriceInformation($amount, $articleId, $shopId = 1)
    {

        $query = $this->createQueryBuilder();
        $query->select('p.oxartid', 'p.oxaddabs', 'p.oxaddperc')
            ->from('oxprice2article', 'p')
            ->from('oxarticles', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.oxid', ':id'),
                    $query->expr()->lte('p.oxamount', ':amount'),
                    $query->expr()->gte('p.oxamountto', ':amount')
                ),
                $query->expr()->eq('p.oxshopid', ':shopid'),
                $query->expr()->orX(
                    $query->expr()->eq('p.oxartid', 'a.oxid'),
                    $query->expr()->eq('p.oxartid', 'a.oxparentid')
                )
            )
            ->setParameter(':id', $articleId)
            ->setParameter(':amount', $amount)
            ->setParameter(':shopid', $shopId);

        $sth = $query->execute();
        $result = $sth->fetchAll();

        if (sizeof($result) == 0) {
            return new BulkPriceInfo(null);
        }
        if (sizeof($result) > 2) {
            throw new \Exception(
                'Data in oxprice2article is not consistent. ' .
                "Several results for amount $amount, article id $articleId and shop id $shopId"
            );
        }
        // We have only one result - it does not matter whether child or parent
        if (sizeof($result) == 1) {
            return new BulkPriceInfo($result[0]);
        }

        // Assert that we really have parent and child
        if ($result[0]['oxartid'] == $result[1]['oxartid']) {
            throw new \Exception(
                'Data in oxprice2article is not consistent. ' .
                "Several results for amount $amount, article id $articleId and shop id $shopId"
            );
        };

        // We have a result both for parent and child, so we prefer the child
        if ($result[0]['oxartid'] == $articleId) {
            return new BulkPriceInfo($result[0]);
        } else {
            return new BulkPriceInfo($result[1]);
        }
    }

    public function getGroupPrice($priceGroup, $articleId, $shopId = 1)
    {

        return $this->getBasicPriceInformation($articleId, $shopId)->getGroupPrice($priceGroup);
    }

    public function getBasicPriceInformation($articleId, $shopId = 1)
    {

        $query = $this->createQueryBuilder();
        $query->select('oxid', 'oxparentid', 'oxprice', 'oxpricea', 'oxpriceb', 'oxpricec', 'oxtprice', 'oxvat')
            ->from($this->tablename)
            ->andWhere('oxid = :id')
            ->andWhere('oxshopid = :shopid')
            ->setParameter(':id', $articleId)
            ->setParameter(':shopid', $shopId);

        $sth = $query->execute();
        $result = $sth->fetchAll();

        if (sizeof($result) == 0) {
            throw new \Exception("No price information found for article with id $articleId");
        }

        $childData = $parentData = $result[0];
        if ($childData['oxparentid']) {
            $query->setParameter(':id', $childData['oxparentid']);
            $sth = $query->execute();
            $result = $sth->fetchAll();
            if (sizeof($result) == 0) {
                throw new \Exception("Did not find parent for article with id $articleId");
            }
            $parentData = $result[0];
        }

        return new BasicPriceInformation($childData, $parentData);
    }

    public function getVatFromCategory($articleId, $shopId = 1)
    {

        $query = $this->createQueryBuilder();
        $query->select('c.oxvat')
            ->from($this->getViewNameForTable('oxcategories', false), 'c')
            ->from($this->getViewNameForTable('oxobject2category', false), 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('c.oxid', 'a.oxcatnid'),
                    $query->expr()->eq('a.oxobjectid', $articleId),
                    $query->expr()->isNotNull('c.oxvat')
                )
            );

        $sth = $query->execute();
        $result = $sth->fetchAll();

        if (sizeof($result) == 0) {
            return null;
        }
        if (sizeof($result) > 1) {
            throw new \Exception('Ambigious vat information via categories for article with id ' . $articleId);
        }

        return $result[0]['oxvat'];
    }


}
