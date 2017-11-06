<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 25.09.17
 * Time: 15:47
 */

namespace OxidEsales\EshopCommunity\Internal\Dao;

use Doctrine\DBAL\Connection;
use OxidEsales\EshopCommunity\Internal\DataObject\SelectList;
use OxidEsales\EshopCommunity\Internal\DataObject\SelectListItem;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Internal\Utility\OxidLegacyServiceInterface;

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
     * @return SelectList
     */
    public function getSelectListForArticle($articleId)
    {

        $selectionValues = $this->getSelectListValuesForChild($articleId);
        if (sizeof($selectionValues) == 0) {
            $selectionValues = $this->getSelectListValuesForParent($articleId);
        }

        $selections = [];
        foreach ($selectionValues as $selection) {
            $items = [];
            foreach ($selection as $values) {
                $items[] = $this->valuesToItem($articleId, $values);
            }
            $selections[] = $items;
        }

        return new SelectList($selections);
    }

    private function getSelectListValuesForChild($articleId)
    {

        $query = $this->createQueryBuilder();
        $query->select('oxvaldesc')
            ->from($this->getViewName(false), 'sl')
            ->join('sl', 'oxobject2selectlist', 'j', 'sl.oxid = j.oxselnid')
            ->where($query->expr()->eq('j.oxobjectid', ':articleid'))
            ->setParameter(':articleid', $articleId)
            ->orderBy('j.oxsort');

        $sth = $query->execute();

        return $this->prepareSelectList($sth->fetchAll());

    }

    private function getSelectListValuesForParent($articleId)
    {

        $query = $this->createQueryBuilder();
        $query->select('oxvaldesc')
            ->from($this->getViewName(false), 'sl')
            ->join('sl', 'oxobject2selectlist', 'j', 'sl.oxid = j.oxselnid')
            ->join('j', 'oxarticles', 'ja', 'ja.oxparentid = j.oxobjectid')
            ->where($query->expr()->eq('ja.oxid', ':articleid'))
            ->setParameter(':articleid', $articleId)
            ->orderBy('j.oxsort');

        $sth = $query->execute();

        return $this->prepareSelectList($sth->fetchAll());
    }

    private function valuesToItem($articleId, $values)
    {
        if (sizeof($values) == 2) {
            // No price delta given
            return new SelectListItem($articleId, $values[1], 0, SelectListItem::DELTA_TYPE_ABSOLUTE);
        }

        return new SelectListItem(
            $articleId, $values[1], $values[3],
            $values[4] == '%' ? SelectListItem::DELTA_TYPE_PERCENT : SelectListItem::DELTA_TYPE_ABSOLUTE
        );
    }

    /**
     * @param $rows
     * @param $articleId
     *
     * @return array[]
     */
    private function prepareSelectList($rows)
    {
        $selections = [];

        foreach ($rows as $row) {

            $itemDescriptions = $this->explodeToTwoDimensionalArray($row['OXVALDESC']);
            $items = [];
            foreach ($itemDescriptions as $itemDescription) {
                $matches = [];
                // The string looks like '[field key]!P![price][%]' where the % is optional
                if (preg_match('/^(.*?)(!P!(.*?)(%?))?$/', trim($itemDescription[0]), $matches)) {
                    $items[] = $matches;
                }
            };
            $selections[] = $items;
        }

        return $selections;
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