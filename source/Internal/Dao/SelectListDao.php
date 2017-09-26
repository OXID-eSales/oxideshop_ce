<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 25.09.17
 * Time: 15:47
 */

namespace OxidEsales\EshopCommunity\Internal\Dao;

use Doctrine\DBAL\Connection;
use OxidEsales\EshopCommunity\Internal\DataObject\SelectListItem;
use OxidEsales\EshopCommunity\Internal\Utilities\ContextInterface;
use OxidEsales\EshopCommunity\Internal\Utilities\OxidLegacyServiceInterface;

class SelectListDao extends BaseDao implements SelectListDaoInterface
{

    /**
     * SelectListDao constructor.
     *
     * @param Connection                 $connection
     * @param ContextInterface           $context
     * @param OxidLegacyServiceInterface $legacyService
     */
    public function __construct(Connection $connection,
                                ContextInterface $context,
                                OxidLegacyServiceInterface $legacyService)
    {
        parent::__construct('oxselectlist', $connection, $context, $legacyService);
    }

    /**
     * @param $articleId
     *
     * @return SelectListItem[]
     */
    public function getSelectListForArticle($articleId)
    {

        $selectListValues = $this->getSelectListValuesForChild($articleId);
        if (sizeof($selectListValues) == 0) {
            $selectListValues = $this->getSelectListValuesForParent($articleId);
        }

        $items = [];
        foreach ($selectListValues as $values) {
            $items[] = $this->valuesToItem($articleId, $values);
        }

        return $items;
    }

    private function getSelectListValuesForChild($articleId)
    {

        $query = $this->createQueryBuilder();
        $query->select('oxvaldesc')
            ->from($this->getViewName(false), 'sl')
            ->join('sl', 'oxobject2selectlist', 'j', 'sl.oxid = j.oxselnid')
            ->where($query->expr()->eq('j.oxobjectid', ':articleid'))
            ->setParameter(':articleid', $articleId);

        $sth = $query->execute();

        return $this->parseListResult($sth->fetchAll());

        return $items;
    }

    private function getSelectListValuesForParent($articleId)
    {

        $query = $this->createQueryBuilder();
        $query->select('oxvaldesc')
            ->from($this->getViewName(false), 'sl')
            ->join('sl', 'oxobject2selectlist', 'j', 'sl.oxid = j.oxselnid')
            ->join('j', 'oxarticles', 'ja', 'ja.oxparentid = j.oxobjectid')
            ->where($query->expr()->eq('ja.oxid', ':articleid'))
            ->setParameter(':articleid', $articleId);

        $sth = $query->execute();

        return $this->parseListResult($sth->fetchAll());
    }

    private function valuesToItem($articleId, $values)
    {

        return new SelectListItem(
            $articleId, $values[1], $values[2],
            $values[3] == '%' ? SelectListItem::DELTA_TYPE_PERCENT : SelectListItem::DELTA_TYPE_ABSOLUTE
        );
    }

    /**
     * @param $rows
     * @param $articleId
     *
     * @return array[]
     */
    private function parseListResult($rows)
    {

        $items = [];

        foreach ($rows as $row) {

            $itemDescriptions = $this->explodeToTwoDimensionalArray($row['OXVALDESC']);

            foreach ($itemDescriptions as $itemDescription) {
                $matches = [];
                // The string looks like '[field key]!P![price][%]' where the % is optional
                if (preg_match('/^(.*?)!P!(.*?)(%?)$/', trim($itemDescription[0]), $matches)) {
                    $items[] = $matches;
                }
            }
        }

        return $items;
    }

    private function explodeToTwoDimensionalArray($input, $delimiters = ['@@', '__'])
    {

        $resultArray = explode($delimiters[0], $input);
        for ($i = 0; $i < sizeof($resultArray); $i++) {
            $resultArray[$i] = explode($delimiters[1], $resultArray[$i]);
        }

        return $resultArray;
    }

}