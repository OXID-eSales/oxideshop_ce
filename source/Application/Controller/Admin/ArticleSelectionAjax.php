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
 * Class controls article assignment to selection lists
 */
class ArticleSelectionAjax extends \ajaxListComponent
{

    /**
     * Columns array
     *
     * @var array
     */
    protected $_aColumns = array('container1' => array( // field , table,         visible, multilanguage, ident
        array('oxtitle', 'oxselectlist', 1, 1, 0),
        array('oxident', 'oxselectlist', 1, 0, 0),
        array('oxvaldesc', 'oxselectlist', 1, 0, 0),
        array('oxid', 'oxselectlist', 0, 0, 1)
    ),
                                 'container2' => array(
                                     array('oxtitle', 'oxselectlist', 1, 1, 0),
                                     array('oxident', 'oxselectlist', 1, 0, 0),
                                     array('oxvaldesc', 'oxselectlist', 1, 0, 0),
                                     array('oxid', 'oxobject2selectlist', 0, 0, 1)
                                 )
    );

    /**
     * Returns SQL query for data to fetc
     *
     * @return string
     */
    protected function _getQuery()
    {
        $sSLViewName = $this->_getViewName('oxselectlist');
        $sArtViewName = $this->_getViewName('oxarticles');
        $oDb = oxDb::getDb();

        $sArtId = oxRegistry::getConfig()->getRequestParameter('oxid');
        $sSynchArtId = oxRegistry::getConfig()->getRequestParameter('synchoxid');

        $sOxid = ($sArtId) ? $sArtId : $sSynchArtId;
        $sQ = "select oxparentid from {$sArtViewName} where oxid = " . $oDb->quote($sOxid) . " and oxparentid != '' ";
        $sQ .= "and (select count(oxobjectid) from oxobject2selectlist " .
               "where oxobjectid = " . $oDb->quote($sOxid) . ") = 0";
        // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804).
        $sParentId = oxDb::getMaster()->getOne($sQ);

        // all selectlists article is in
        $sQAdd = " from oxobject2selectlist left join {$sSLViewName} " .
                 "on {$sSLViewName}.oxid=oxobject2selectlist.oxselnid  " .
                 "where oxobject2selectlist.oxobjectid = " . $oDb->quote($sOxid) . " ";
        if ($sParentId) {
            $sQAdd .= "or oxobject2selectlist.oxobjectid = " . $oDb->quote($sParentId) . " ";
        }
        // all not assigned selectlists
        if ($sSynchArtId) {
            $sQAdd = " from {$sSLViewName}  " .
                     "where {$sSLViewName}.oxid not in ( select oxobject2selectlist.oxselnid {$sQAdd} ) ";
        }

        return $sQAdd;
    }

    /**
     * Removes article selection lists.
     */
    public function removeSel()
    {
        $aChosenArt = $this->_getActionIds('oxobject2selectlist.oxid');
        if (oxRegistry::getConfig()->getRequestParameter('all')) {

            $sQ = $this->_addFilter("delete oxobject2selectlist.* " . $this->_getQuery());
            oxDb::getDb()->Execute($sQ);
        } elseif (is_array($aChosenArt)) {
            $sChosenArticles = implode(", ", oxDb::getDb()->quoteArray($aChosenArt));
            $sQ = "delete from oxobject2selectlist " .
                  "where oxobject2selectlist.oxid in (" . $sChosenArticles . ") ";
            oxDb::getDb()->Execute($sQ);
        }

        $articleId = oxRegistry::getConfig()->getRequestParameter('oxid');
        $this->onArticleSelectionListChange($articleId);
    }

    /**
     * Adds selection lists to article.
     *
     * @throws Exception
     */
    public function addSel()
    {
        $aAddSel = $this->_getActionIds('oxselectlist.oxid');
        $soxId = oxRegistry::getConfig()->getRequestParameter('synchoxid');

        // adding
        if (oxRegistry::getConfig()->getRequestParameter('all')) {
            $sSLViewName = $this->_getViewName('oxselectlist');
            $aAddSel = $this->_getAll($this->_addFilter("select $sSLViewName.oxid " . $this->_getQuery()));
        }

        if ($soxId && $soxId != "-1" && is_array($aAddSel)) {

            oxDb::getDb()->startTransaction();
            try {
                $database = oxDb::getDb();
                foreach ($aAddSel as $sAdd) {
                    $oNew = oxNew("oxBase");
                    $oNew->init("oxobject2selectlist");
                    $sObjectIdField = 'oxobject2selectlist__oxobjectid';
                    $sSelectetionIdField = 'oxobject2selectlist__oxselnid';
                    $sOxSortField = 'oxobject2selectlist__oxsort';
                    $oNew->$sObjectIdField = new oxField($soxId);
                    $oNew->$sSelectetionIdField = new oxField($sAdd);
                    $sSql = "select max(oxsort) + 1 from oxobject2selectlist where oxobjectid =  {$database->quote($soxId)} ";
                    // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804).
                    $oNew->$sOxSortField = new oxField(( int ) $database->getOne($sSql));
                    $oNew->save();
                }
            } catch (Exception $exception) {
                oxDb::getDb()->rollbackTransaction();
                throw $exception;
            }
            oxDb::getDb()->commitTransaction();

            $this->onArticleSelectionListChange($soxId);
        }
    }

    /**
     * Method is used to bind to article selection list change.
     *
     * @param string $articleId
     */
    protected function onArticleSelectionListChange($articleId)
    {
    }
}
