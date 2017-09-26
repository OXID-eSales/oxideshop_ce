<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 25.09.17
 * Time: 15:47
 */

namespace OxidEsales\EshopCommunity\Internal\Dao;


use OxidEsales\EshopCommunity\Internal\DataObject\SelectListItem;

class SelectListDao extends BaseDao implements SelectListDaoInterface
{

    public function __construct($connection, $context, $legacyService) {

        parent::__construct('oxselectlist', $connection, $context, $legacyService);

    }

    public function getSelectListForArticle($articleId) {

        $query = $this->createQueryBuilder();
        $query->select('oxvaldesc')
            ->from($this->getViewName(false), 'sl')
            ->join('sl', 'oxobject2selectlist', 'j', 'sl.oxid = j.oxselnid')
            ->where($query->expr()->eq('j.oxobjectid', ':articleid'))
            ->setParameter(':articleid', $articleId);

        $sth = $query->execute();

        $items = [];
        foreach ($this->parseListResult($sth->fetchAll()) as $values) {
            $items[] = new SelectListItem($articleId, $values[1], $values[2],
                $values[3] == '%' ? SelectListItem::DELTA_TYPE_PERCENT : SelectListItem::DELTA_TYPE_ABSOLUTE);
        }

        return $items;

    }

    /**
     * @param $rows
     * @param $articleId
     *
     * @return array[]
     */
    private function parseListResult($rows) {

        $items = [];

        foreach( $rows as $row ) {

            $itemDescriptions = $this->explodeToTwoDimensionalArray($row['OXVALDESC']);

            foreach ( $itemDescriptions as $itemDescription ) {
                $matches = [];
                // The string looks like '[field key]!P![price][%]' where the % is optional
                if (preg_match('/^(.*?)!P!(.*?)(%?)$/', trim($itemDescription[0]), $matches)) {
                    $items[] = $matches;
                }
            }

        }

        return $items;

    }

    private function explodeToTwoDimensionalArray($input, $delimiters=['@@', '__']) {

        $resultArray = explode($delimiters[0], $input);
        for ($i = 0; $i < sizeof($resultArray); $i++) {
            $resultArray[$i] = explode($delimiters[1], $resultArray[$i]);
        }
        return $resultArray;
    }

}