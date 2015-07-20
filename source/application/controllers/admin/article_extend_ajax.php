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

/**
 * Class controls article assignment to category
 */
class article_extend_ajax extends ajaxListComponent
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
        $sCategoriesTable = $this->_getViewName('oxcategories');
        $sO2CView = $this->_getViewName('oxobject2category');
        $oDb = oxDb::getDb();

        $sOxid = oxRegistry::getConfig()->getRequestParameter('oxid');
        $sSynchOxid = oxRegistry::getConfig()->getRequestParameter('synchoxid');

        if ($sOxid) {
            // all categories article is in
            $sQAdd = " from $sO2CView left join $sCategoriesTable on $sCategoriesTable.oxid=$sO2CView.oxcatnid ";
            $sQAdd .= " where $sO2CView.oxobjectid = " . $oDb->quote($sOxid)
                      . " and $sCategoriesTable.oxid is not null ";
        } else {
            $sQAdd = " from $sCategoriesTable where $sCategoriesTable.oxid not in ( ";
            $sQAdd .= " select $sCategoriesTable.oxid from $sO2CView "
                      . "left join $sCategoriesTable on $sCategoriesTable.oxid=$sO2CView.oxcatnid ";
            $sQAdd .= " where $sO2CView.oxobjectid = " . $oDb->quote($sSynchOxid)
                      . " and $sCategoriesTable.oxid is not null ) and $sCategoriesTable.oxpriceto = '0'";
        }

        return $sQAdd;
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
            $query = $this->onRemovingCategoriesUpdateQuery($query);
            $query .= " oxcatnid in (" . implode(', ', oxDb::getInstance()->quoteArray($categoriesToRemove)) . ')';
            $dataBase->Execute($query);

            // updating oxtime values
            $this->_updateOxTime($oxId);
        }

        $this->resetArtSeoUrl($oxId, $categoriesToRemove);
        $this->resetContentCache();

        $this->onRemovingCategoriesAdditionalActions($categoriesToRemove, $oxId);
    }

    /**
     * Adds article to chosen category
     */
    public function addCat()
    {
        $myConfig = $this->getConfig();
        $categoriesToAdd = $this->_getActionIds('oxcategories.oxid');
        $soxId = oxRegistry::getConfig()->getRequestParameter('synchoxid');
        $sShopID = $myConfig->getShopId();
        $sO2CView = $this->_getViewName('oxobject2category');

        // adding
        if (oxRegistry::getConfig()->getRequestParameter('all')) {
            $sCategoriesTable = $this->_getViewName('oxcategories');
            $categoriesToAdd = $this->_getAll($this->_addFilter("select $sCategoriesTable.oxid " . $this->_getQuery()));
        }

        if (isset($categoriesToAdd) && is_array($categoriesToAdd)) {
            $oDb = oxDb::getDb();

            $oNew = oxNew('oxobject2category');

            foreach ($categoriesToAdd as $sAdd) {
                // check, if it's already in, then don't add it again
                $sSelect = "select 1 from " . $sO2CView . " as oxobject2category where oxobject2category.oxcatnid= "
                           . $oDb->quote($sAdd) . " and oxobject2category.oxobjectid = " . $oDb->quote($soxId) . " ";
                if ($oDb->getOne($sSelect, false, false)) {
                    continue;
                }

                $oNew->setId(md5($soxId . $sAdd . $sShopID));
                $oNew->oxobject2category__oxobjectid = new oxField($soxId);
                $oNew->oxobject2category__oxcatnid = new oxField($sAdd);
                $oNew->oxobject2category__oxtime = new oxField(time());

                $oNew->save();
            }

            $this->_updateOxTime($soxId);

            $this->resetArtSeoUrl($soxId);
            $this->resetContentCache();
            $this->onAddingCategories($categoriesToAdd);
        }
    }

    /**
     * Updates oxtime value for product
     *
     * @param string $soxId product id
     */
    protected function _updateOxTime($soxId)
    {
        $oDb = oxDb::getDb();
        $sO2CView = $this->_getViewName('oxobject2category');
        $soxId = $oDb->quote($soxId);
        $sqlShopFilter = $this->onUpdatingOxTimeGetQueryToEmbed();
        // updating oxtime values
        $sQ = "update oxobject2category set oxtime = 0 where oxobjectid = {$soxId} {$sqlShopFilter} and oxid = (
                    select oxid from (
                        select oxid from {$sO2CView} where oxobjectid = {$soxId} {$sqlShopFilter}
                        order by oxtime limit 1
                    ) as _tmp
                )";
        $oDb->execute($sQ);
    }

    /**
     * Sets selected category as a default
     */
    public function setAsDefault()
    {
        $sDefCat = oxRegistry::getConfig()->getRequestParameter("defcat");
        $soxId = oxRegistry::getConfig()->getRequestParameter("oxid");
        $oDb = oxDb::getDb();

        $sQuotedOxId = $oDb->quote($soxId);
        $sQuotedDefCat = $oDb->quote($sDefCat);

        $sqlShopFilter = $this->onSettingCategoryAsDefaultGetQueryToEmbed();

        // #0003650: increment all product references independent to active shop
        $sQ = "update oxobject2category set oxtime = oxtime + 10 where oxobjectid = {$sQuotedOxId} {$sqlShopFilter}";
        oxDb::getInstance()->getDb()->Execute($sQ);

        // set main category for active shop
        $sQ = "update oxobject2category set oxtime = 0 where oxobjectid = {$sQuotedOxId} " .
              "and oxcatnid = {$sQuotedDefCat} {$sqlShopFilter}";
        oxDb::getInstance()->getDb()->Execute($sQ);
        //echo "\n$sQ\n";

        // #0003366: invalidate article SEO for all shops
        oxRegistry::get("oxSeoEncoder")->markAsExpired($soxId, null, 1, null, "oxtype='oxarticle'");
        $this->resetContentCache();
    }

    /**
     * Method used for overloading and embed query.
     *
     * @param string $query
     *
     * @return string
     */
    protected function onRemovingCategoriesUpdateQuery($query)
    {
        return $query;
    }

    /**
     * Method is used for overloading to do additional actions.
     *
     * @param array  $categoriesToRemove
     * @param string $oxId
     */
    protected function onRemovingCategoriesAdditionalActions($categoriesToRemove, $oxId)
    {
    }

    /**
     * Method is used for overloading.
     *
     * @param array $categories
     */
    protected function onAddingCategories($categories)
    {
    }

    /**
     * Method is used for overloading to insert additional query condition.
     *
     * @return string
     */
    protected function onUpdatingOxTimeGetQueryToEmbed()
    {
        return '';
    }

    /**
     * Method is used for overloading to insert additional query condition.
     *
     * @return string
     */
    protected function onSettingCategoryAsDefaultGetQueryToEmbed()
    {
        return '';
    }
}
