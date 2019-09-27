<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use oxDb;
use oxField;
use Exception;

/**
 * Class controls article assignment to category.
 */
class ArticleExtendAjax extends \OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax
{
    /**
     * Columns array
     *
     * @var array
     */
    protected $_aColumns = ['container1' => [ // field , table,         visible, multilanguage, ident
        ['oxtitle', 'oxcategories', 1, 1, 0],
        ['oxdesc', 'oxcategories', 1, 1, 0],
        ['oxid', 'oxcategories', 0, 0, 0],
        ['oxid', 'oxcategories', 0, 0, 1]
    ],
                                 'container2' => [
                                     ['oxtitle', 'oxcategories', 1, 1, 0],
                                     ['oxdesc', 'oxcategories', 1, 1, 0],
                                     ['oxid', 'oxcategories', 0, 0, 0],
                                     ['oxid', 'oxobject2category', 0, 0, 1],
                                     ['oxtime', 'oxobject2category', 0, 0, 1],
                                     ['oxid', 'oxcategories', 0, 0, 1]
                                 ],
    ];

    /**
     * Returns SQL query for data to fetc
     *
     * @return string
     */
    protected function _getQuery()
    {
        $categoriesTable = $this->_getViewName('oxcategories');
        $objectToCategoryView = $this->_getViewName('oxobject2category');
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $oxId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('oxid');
        $synchOxid = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('synchoxid');

        if ($oxId) {
            // all categories article is in
            $query = " from $objectToCategoryView left join $categoriesTable on $categoriesTable.oxid=$objectToCategoryView.oxcatnid ";
            $query .= " where $objectToCategoryView.oxobjectid = " . $database->quote($oxId)
                      . " and $categoriesTable.oxid is not null ";
        } else {
            $query = " from $categoriesTable where $categoriesTable.oxid not in ( ";
            $query .= " select $categoriesTable.oxid from $objectToCategoryView "
                      . "left join $categoriesTable on $categoriesTable.oxid=$objectToCategoryView.oxcatnid ";
            $query .= " where $objectToCategoryView.oxobjectid = " . $database->quote($synchOxid)
                      . " and $categoriesTable.oxid is not null ) and $categoriesTable.oxpriceto = '0'";
        }

        return $query;
    }

    /**
     * Returns array with DB records
     *
     * @param string $sQ SQL query
     *
     * @return array
     */
    protected function _getDataFields($sQ)
    {
        $dataFields = parent::_getDataFields($sQ);
        if (\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('oxid') && is_array($dataFields) && count($dataFields)) {
            // looking for smallest time value to mark record as main category ..
            $minimalPosition = null;
            $minimalValue = null;
            reset($dataFields);
            foreach ($dataFields as $position => $fields) {
                // already set ?
                if ($fields['_3'] == '0') {
                    $minimalPosition = null;
                    break;
                }

                if (!$minimalValue) {
                    $minimalValue = $fields['_3'];
                    $minimalPosition = $position;
                } elseif ($minimalValue > $fields['_3']) {
                    $minimalPosition = $position;
                }
            }

            // setting primary category
            if (isset($minimalPosition)) {
                $dataFields[$minimalPosition]['_3'] = '0';
            }
        }

        return $dataFields;
    }

    /**
     * Removes article from chosen category
     */
    public function removeCat()
    {
        $categoriesToRemove = $this->_getActionIds('oxcategories.oxid');

        $oxId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('oxid');
        $dataBase = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        // adding
        if (\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('all')) {
            $categoriesTable = $this->_getViewName('oxcategories');
            $categoriesToRemove = $this->_getAll($this->_addFilter("select {$categoriesTable}.oxid " . $this->_getQuery()));
        }

        // removing all
        if (is_array($categoriesToRemove) && count($categoriesToRemove)) {
            $query = "delete from oxobject2category where oxobject2category.oxobjectid = :oxobjectid and ";
            $query = $this->updateQueryForRemovingArticleFromCategory($query);
            $query .= " oxcatnid in (" . implode(', ', \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray($categoriesToRemove)) . ')';
            $dataBase->Execute($query, [
                ':oxobjectid' => $oxId
            ]);

            // updating oxtime values
            $this->_updateOxTime($oxId);
        }

        $this->resetArtSeoUrl($oxId, $categoriesToRemove);
        $this->resetContentCache();

        $this->onCategoriesRemoval($categoriesToRemove, $oxId);
    }

    /**
     * Adds article to chosen category
     *
     * @throws Exception
     */
    public function addCat()
    {
        $config = $this->getConfig();
        $categoriesToAdd = $this->_getActionIds('oxcategories.oxid');
        $oxId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('synchoxid');
        $shopId = $config->getShopId();
        $objectToCategoryView = $this->_getViewName('oxobject2category');

        // adding
        if (\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('all')) {
            $categoriesTable = $this->_getViewName('oxcategories');
            $categoriesToAdd = $this->_getAll($this->_addFilter("select $categoriesTable.oxid " . $this->_getQuery()));
        }

        if (isset($categoriesToAdd) && is_array($categoriesToAdd)) {
            // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804 and ESDEV-3822).
            $database = \OxidEsales\Eshop\Core\DatabaseProvider::getMaster();

            $objectToCategory = oxNew(\OxidEsales\Eshop\Application\Model\Object2Category::class);

            foreach ($categoriesToAdd as $sAdd) {
                // check, if it's already in, then don't add it again
                $sSelect = "select 1 from " . $objectToCategoryView . " as oxobject2category " .
                    "where oxobject2category.oxcatnid = :oxcatnid " .
                    "and oxobject2category.oxobjectid = :oxobjectid";
                if ($database->getOne($sSelect, [':oxcatnid' => $sAdd, ':oxobjectid' => $oxId])) {
                    continue;
                }

                $objectToCategory->setId(md5($oxId . $sAdd . $shopId));
                $objectToCategory->oxobject2category__oxobjectid = new \OxidEsales\Eshop\Core\Field($oxId);
                $objectToCategory->oxobject2category__oxcatnid = new \OxidEsales\Eshop\Core\Field($sAdd);
                $objectToCategory->oxobject2category__oxtime = new \OxidEsales\Eshop\Core\Field(time());

                $objectToCategory->save();
            }

            $this->_updateOxTime($oxId);

            $this->resetArtSeoUrl($oxId);
            $this->resetContentCache();
            $this->onCategoriesAdd($categoriesToAdd);
        }
    }

    /**
     * Updates oxtime value for product
     *
     * @param string $oxId product id
     */
    protected function _updateOxTime($oxId)
    {
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $objectToCategoryView = $this->_getViewName('oxobject2category');
        $queryToEmbed = $this->formQueryToEmbedForUpdatingTime();

        // updating oxtime values
        $query = "update oxobject2category set oxtime = 0 where oxobjectid = :oxobjectid {$queryToEmbed} and oxid = (
                    select oxid from (
                        select oxid from {$objectToCategoryView} where oxobjectid = :oxobjectid {$queryToEmbed}
                        order by oxtime limit 1
                    ) as _tmp
                )";
        $database->execute($query, [':oxobjectid' => $oxId]);
    }

    /**
     * Sets selected category as a default
     */
    public function setAsDefault()
    {
        $defCat = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("defcat");
        $oxId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("oxid");

        $queryToEmbed = $this->formQueryToEmbedForSettingCategoryAsDefault();

        // #0003650: increment all product references independent to active shop
        $query = "update oxobject2category set oxtime = oxtime + 10 where oxobjectid = :oxobjectid {$queryToEmbed}";
        \OxidEsales\Eshop\Core\DatabaseProvider::getInstance()->getDb()->execute($query, [':oxobjectid' => $oxId]);

        // set main category for active shop
        $query = "update oxobject2category set oxtime = 0
                  where oxobjectid = :oxobjectid and oxcatnid = :oxcatnid {$queryToEmbed}";
        \OxidEsales\Eshop\Core\DatabaseProvider::getInstance()->getDb()->execute($query, [
            ':oxobjectid' => $oxId,
            ':oxcatnid' => $defCat
        ]);
        //echo "\n$sQ\n";

        // #0003366: invalidate article SEO for all shops
        \OxidEsales\Eshop\Core\Registry::getSeoEncoder()->markAsExpired($oxId, null, 1, null, "oxtype='oxarticle'");
        $this->resetContentCache();
    }

    /**
     * Method used for overloading and embed query.
     *
     * @param string $query
     *
     * @return string
     */
    protected function updateQueryForRemovingArticleFromCategory($query)
    {
        return $query;
    }

    /**
     * Method is used for overloading to do additional actions.
     *
     * @param array  $categoriesToRemove
     * @param string $oxId
     */
    protected function onCategoriesRemoval($categoriesToRemove, $oxId)
    {
    }

    /**
     * Method is used for overloading.
     *
     * @param array $categories
     */
    protected function onCategoriesAdd($categories)
    {
    }

    /**
     * Method is used for overloading to insert additional query condition.
     *
     * @return string
     */
    protected function formQueryToEmbedForUpdatingTime()
    {
        return '';
    }

    /**
     * Method is used for overloading to insert additional query condition.
     *
     * @return string
     */
    protected function formQueryToEmbedForSettingCategoryAsDefault()
    {
        return '';
    }
}
