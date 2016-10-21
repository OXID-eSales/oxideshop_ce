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

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxDb;

/**
 * Class manages manufacturer assignment to articles
 */
class ManufacturerMainAjax extends \ajaxListComponent
{

    /**
     * If true extended column selection will be build
     *
     * @var bool
     */
    protected $_blAllowExtColumns = true;

    /**
     * Columns array
     *
     * @var array
     */
    protected $_aColumns = array(
        // field , table, visible, multilanguage, id
        'container1' => array(
            array('oxartnum', 'oxarticles', 1, 0, 0),
            array('oxtitle', 'oxarticles', 1, 1, 0),
            array('oxean', 'oxarticles', 1, 0, 0),
            array('oxmpn', 'oxarticles', 0, 0, 0),
            array('oxprice', 'oxarticles', 0, 0, 0),
            array('oxstock', 'oxarticles', 0, 0, 0),
            array('oxid', 'oxarticles', 0, 0, 1)
        ),
        'container2' => array(
            array('oxartnum', 'oxarticles', 1, 0, 0),
            array('oxtitle', 'oxarticles', 1, 1, 0),
            array('oxean', 'oxarticles', 1, 0, 0),
            array('oxmpn', 'oxarticles', 0, 0, 0),
            array('oxprice', 'oxarticles', 0, 0, 0),
            array('oxstock', 'oxarticles', 0, 0, 0),
            array('oxid', 'oxarticles', 0, 0, 1)
        )
    );

    /**
     * Returns SQL query for data to fetc
     *
     * @return string
     */
    protected function _getQuery()
    {
        $config = $this->getConfig();

        // looking for table/view
        $articlesViewName = $this->_getViewName('oxarticles');
        $objectToCategoryViewName = $this->_getViewName('oxobject2category');
        $database = oxDb::getDb();

        $manufacturerId = $config->getRequestParameter('oxid');
        $syncedManufacturerId = $config->getRequestParameter('synchoxid');

        // Manufacturer selected or not ?
        if (!$manufacturerId) {
            // performance
            $query = ' from ' . $articlesViewName . ' where ' . $articlesViewName . '.oxshopid="' . $config->getShopId() . '" and 1 ';
            $query .= $config->getRequestParameter('blVariantsSelection') ? '' : " and $articlesViewName.oxparentid = '' and $articlesViewName.oxmanufacturerid != " . $database->quote($syncedManufacturerId);
        } elseif ($syncedManufacturerId && $syncedManufacturerId != $manufacturerId) {
            // selected category ?
            $query = " from $objectToCategoryViewName left join $articlesViewName on ";
            $query .= $config->getRequestParameter('blVariantsSelection') ? " ( $articlesViewName.oxid = $objectToCategoryViewName.oxobjectid or $articlesViewName.oxparentid = $objectToCategoryViewName.oxobjectid )" : " $articlesViewName.oxid = $objectToCategoryViewName.oxobjectid ";
            $query .= 'where ' . $articlesViewName . '.oxshopid="' . $config->getShopId() . '" and ' . $objectToCategoryViewName . '.oxcatnid = ' . $database->quote($manufacturerId) . ' and ' . $articlesViewName . '.oxmanufacturerid != ' . $database->quote($syncedManufacturerId);
            $query .= $config->getRequestParameter('blVariantsSelection') ? '' : " and $articlesViewName.oxparentid = '' ";
        } else {
            $query = " from $articlesViewName where $articlesViewName.oxmanufacturerid = " . $database->quote($manufacturerId);
            $query .= $config->getRequestParameter('blVariantsSelection') ? '' : " and $articlesViewName.oxparentid = '' ";
        }

        return $query;
    }

    /**
     * Adds filter SQL to current query
     *
     * @param string $query query to add filter condition
     *
     * @return string
     */
    protected function _addFilter($query)
    {
        $articleViewName = $this->_getViewName('oxarticles');
        $query = parent::_addFilter($query);

        // display variants or not ?
        $query .= $this->getConfig()->getRequestParameter('blVariantsSelection') ? ' group by ' . $articleViewName . '.oxid ' : '';

        return $query;
    }

    /**
     * Removes article from Manufacturer config
     */
    public function removeManufacturer()
    {
        $config = $this->getConfig();
        $articleIds = $this->_getActionIds('oxarticles.oxid');
        $manufacturerId = $config->getRequestParameter('oxid');

        if ($this->getConfig()->getRequestParameter("all")) {
            $articleViewTable = $this->_getViewName('oxarticles');
            $articleIds = $this->_getAll($this->_addFilter("select $articleViewTable.oxid " . $this->_getQuery()));
        }

        if (is_array($articleIds) && !empty($articleIds)) {
            $query = $this->formManufacturerRemovalQuery($articleIds);
            oxDb::getDb()->execute($query);

            $this->resetCounter("manufacturerArticle", $manufacturerId);
        }
    }

    /**
     * Forms and returns query for manufacturers removal.
     *
     * @param array $articlesToRemove Ids of manufacturers which should be removed.
     *
     * @return string
     */
    protected function formManufacturerRemovalQuery($articlesToRemove)
    {
        return "
          UPDATE oxarticles
          SET oxmanufacturerid = null
          WHERE oxid IN ( " . implode(", ", oxDb::getDb()->quoteArray($articlesToRemove)) . ") ";
    }

    /**
     * Adds article to Manufacturer config
     */
    public function addManufacturer()
    {
        $config = $this->getConfig();

        $articleIds = $this->_getActionIds('oxarticles.oxid');
        $manufacturerId = $config->getRequestParameter('synchoxid');

        if ($config->getRequestParameter('all')) {
            $articleViewName = $this->_getViewName('oxarticles');
            $articleIds = $this->_getAll($this->_addFilter("select $articleViewName.oxid " . $this->_getQuery()));
        }

        if ($manufacturerId && $manufacturerId != "-1" && is_array($articleIds)) {
            $database = oxDb::getDb();

            $query = $this->formArticleToManufacturerAdditionQuery($manufacturerId, $articleIds);
            $database->execute($query);
            $this->resetCounter("manufacturerArticle", $manufacturerId);
        }
    }

    /**
     * Forms and returns query for articles addition to manufacturer.
     *
     * @param string $manufacturerId Manufacturer id.
     * @param array  $articlesToAdd  Array of article ids to be added to manufacturer.
     *
     * @return string
     */
    protected function formArticleToManufacturerAdditionQuery($manufacturerId, $articlesToAdd)
    {
        $database = oxDb::getDb();

        return "
            UPDATE oxarticles
            SET oxmanufacturerid = " . $database->quote($manufacturerId) . "
            WHERE oxid IN ( " . implode(", ", $database->quoteArray($articlesToAdd)) . " )";
    }
}
