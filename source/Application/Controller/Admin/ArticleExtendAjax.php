<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

namespace OxidEsales\Eshop\Application\Controller\Admin;

use oxRegistry;
use oxDb;
use oxField;
use Exception;

/**
 * Class controls article assignment to category.
 */
class ArticleExtendAjax extends \ajaxListComponent
{
    /**
     * Columns array
     *
     * @var array
     */
    protected $_aColumns = array('container1' => array( // field , table,         visible, multilanguage, ident
        array('oxtitle', 'oxcategories', 1, 1, 0),
        array('oxdesc', 'oxcategories', 1, 1, 0),
        array('oxid', 'oxcategories', 0, 0, 0),
        array('oxid', 'oxcategories', 0, 0, 1)
    ),
                                 'container2' => array(
                                     array('oxtitle', 'oxcategories', 1, 1, 0),
                                     array('oxdesc', 'oxcategories', 1, 1, 0),
                                     array('oxid', 'oxcategories', 0, 0, 0),
                                     array('oxid', 'oxobject2category', 0, 0, 1),
                                     array('oxtime', 'oxobject2category', 0, 0, 1),
                                     array('oxid', 'oxcategories', 0, 0, 1)
                                 ),
    );

    /**
     * Returns SQL query for data to fetc
     *
     * @return string
     */
    protected function _getQuery()
    {
        $categoriesTable = $this->_getViewName('oxcategories');
        $objectToCategoryView = $this->_getViewName('oxobject2category');
        $database = oxDb::getDb();

        $oxId = oxRegistry::getConfig()->getRequestParameter('oxid');
        $synchOxid = oxRegistry::getConfig()->getRequestParameter('synchoxid');

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
        if (oxRegistry::getConfig()->getRequestParameter('oxid') && is_array($dataFields) && count($dataFields)) {
            // looking for smallest time value to mark record as main category ..
            $minimalPosition = null;
            $minimalValue = null;
            reset($dataFields);
            while (list($position, $fields) = each($dataFields)) {
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

        $oxId = oxRegistry::getConfig()->getRequestParameter('oxid');
        $dataBase = oxDb::getDb();

        // adding
        if (oxRegistry::getConfig()->getRequestParameter('all')) {
            $categoriesTable = $this->_getViewName('oxcategories');
            $categoriesToRemove = $this->_getAll($this->_addFilter("select {$categoriesTable}.oxid " . $this->_getQuery()));
        }

        // removing all
        if (is_array($categoriesToRemove) && count($categoriesToRemove)) {
            $query = "delete from oxobject2category where oxobject2category.oxobjectid= "
                  . oxDb::getDb()->quote($oxId) . " and ";
            $query = $this->updateQueryForRemovingArticleFromCategory($query);
            $query .= " oxcatnid in (" . implode(', ', oxDb::getDb()->quoteArray($categoriesToRemove)) . ')';
            $dataBase->Execute($query);

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
        $oxId = oxRegistry::getConfig()->getRequestParameter('synchoxid');
        $shopId = $config->getShopId();
        $objectToCategoryView = $this->_getViewName('oxobject2category');

        // adding
        if (oxRegistry::getConfig()->getRequestParameter('all')) {
            $categoriesTable = $this->_getViewName('oxcategories');
            $categoriesToAdd = $this->_getAll($this->_addFilter("select $categoriesTable.oxid " . $this->_getQuery()));
        }

        if (isset($categoriesToAdd) && is_array($categoriesToAdd)) {
            oxDb::getDb()->startTransaction();
            try {
                $database = oxDb::getDb();

                $objectToCategory = oxNew('oxobject2category');

                foreach ($categoriesToAdd as $sAdd) {
                    // check, if it's already in, then don't add it again
                    $sSelect = "select 1 from " . $objectToCategoryView . " as oxobject2category where oxobject2category.oxcatnid= "
                               . $database->quote($sAdd) . " and oxobject2category.oxobjectid = " . $database->quote($oxId) . " ";
                    if ($database->getOne($sSelect)) {
                        continue;
                    }

                    $objectToCategory->setId(md5($oxId . $sAdd . $shopId));
                    $objectToCategory->oxobject2category__oxobjectid = new oxField($oxId);
                    $objectToCategory->oxobject2category__oxcatnid = new oxField($sAdd);
                    $objectToCategory->oxobject2category__oxtime = new oxField(time());

                    $objectToCategory->save();
                }
            } catch (Exception $exception) {
                oxDb::getDb()->rollbackTransaction();
                throw $exception;
            }
            oxDb::getDb()->commitTransaction();

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
        $database = oxDb::getDb();
        $objectToCategoryView = $this->_getViewName('oxobject2category');
        $oxId = $database->quote($oxId);
        $queryToEmbed = $this->formQueryToEmbedForUpdatingTime();
        // updating oxtime values
        $query = "update oxobject2category set oxtime = 0 where oxobjectid = {$oxId} {$queryToEmbed} and oxid = (
                    select oxid from (
                        select oxid from {$objectToCategoryView} where oxobjectid = {$oxId} {$queryToEmbed}
                        order by oxtime limit 1
                    ) as _tmp
                )";
        $database->execute($query);
    }

    /**
     * Sets selected category as a default
     */
    public function setAsDefault()
    {
        $defCat = oxRegistry::getConfig()->getRequestParameter("defcat");
        $oxId = oxRegistry::getConfig()->getRequestParameter("oxid");
        $database = oxDb::getDb();

        $quotedOxId = $database->quote($oxId);
        $quotedDefCat = $database->quote($defCat);

        $queryToEmbed = $this->formQueryToEmbedForSettingCategoryAsDefault();

        // #0003650: increment all product references independent to active shop
        $query = "update oxobject2category set oxtime = oxtime + 10 where oxobjectid = {$quotedOxId} {$queryToEmbed}";
        oxDb::getInstance()->getDb()->Execute($query);

        // set main category for active shop
        $query = "update oxobject2category set oxtime = 0 where oxobjectid = {$quotedOxId} " .
              "and oxcatnid = {$quotedDefCat} {$queryToEmbed}";
        oxDb::getInstance()->getDb()->Execute($query);
        //echo "\n$sQ\n";

        // #0003366: invalidate article SEO for all shops
        oxRegistry::get("oxSeoEncoder")->markAsExpired($oxId, null, 1, null, "oxtype='oxarticle'");
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
