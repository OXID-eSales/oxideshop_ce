<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxDb;
use oxRegistry;
use stdClass;

/**
 * Attribute list manager.
 *
 */
class AttributeList extends \OxidEsales\Eshop\Core\Model\ListModel
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct('oxattribute');
    }

    /**
     * Load all attributes by article Id's
     *
     * @param array $aIds article id's
     *
     * @return array $aAttributes;
     */
    public function loadAttributesByIds($aIds)
    {
        if (!count($aIds)) {
            return;
        }

        $sAttrViewName = getViewName('oxattribute');
        $sViewName = getViewName('oxobject2attribute');

        $oxObjectIdsSql = implode(',', \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray($aIds));

        $sSelect = "select $sAttrViewName.oxid, $sAttrViewName.oxtitle, {$sViewName}.oxvalue, {$sViewName}.oxobjectid ";
        $sSelect .= "from {$sViewName} left join $sAttrViewName on $sAttrViewName.oxid = {$sViewName}.oxattrid ";
        $sSelect .= "where {$sViewName}.oxobjectid in ( " . $oxObjectIdsSql . " ) ";
        $sSelect .= "order by {$sViewName}.oxpos, $sAttrViewName.oxpos";

        return $this->_createAttributeListFromSql($sSelect);
    }

    /**
     * Fills array with keys and products with value
     *
     * @param string $sSelect SQL select
     *
     * @return array $aAttributes
     */
    protected function _createAttributeListFromSql($sSelect)
    {
        $aAttributes = [];
        $rs = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->select($sSelect);
        if ($rs != false && $rs->count() > 0) {
            while (!$rs->EOF) {
                if (!isset($aAttributes[$rs->fields[0]])) {
                    $aAttributes[$rs->fields[0]] = new stdClass();
                }

                $aAttributes[$rs->fields[0]]->title = $rs->fields[1];
                if (!isset($aAttributes[$rs->fields[0]]->aProd[$rs->fields[3]])) {
                    $aAttributes[$rs->fields[0]]->aProd[$rs->fields[3]] = new stdClass();
                }
                $aAttributes[$rs->fields[0]]->aProd[$rs->fields[3]]->value = $rs->fields[2];
                $rs->fetchRow();
            }
        }

        return $aAttributes;
    }

    /**
     * Load attributes by article Id
     *
     * @param string $sArticleId article id
     * @param string $sParentId  article parent id
     */
    public function loadAttributes($sArticleId, $sParentId = null)
    {
        if ($sArticleId) {
            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC);

            $sAttrViewName = getViewName('oxattribute');
            $sViewName = getViewName('oxobject2attribute');

            $sSelect = "select {$sAttrViewName}.`oxid`, {$sAttrViewName}.`oxtitle`, o2a.`oxvalue` from {$sViewName} as o2a ";
            $sSelect .= "left join {$sAttrViewName} on {$sAttrViewName}.oxid = o2a.oxattrid ";
            $sSelect .= "where o2a.oxobjectid = :oxobjectid and o2a.oxvalue != '' ";
            $sSelect .= "order by o2a.oxpos, {$sAttrViewName}.oxpos";

            $aAttributes = $oDb->getAll($sSelect, [
                ':oxobjectid' => $sArticleId
            ]);

            if ($sParentId) {
                $aParentAttributes = $oDb->getAll($sSelect, [
                    ':oxobjectid' => $sParentId
                ]);
                $aAttributes = $this->_mergeAttributes($aAttributes, $aParentAttributes);
            }

            $this->assignArray($aAttributes);
        }
    }

    /**
     * Load displayable in baskte/order attributes by article Id
     *
     * @param string $sArtId    article ids
     * @param string $sParentId parent id
     */
    public function loadAttributesDisplayableInBasket($sArtId, $sParentId = null)
    {
        if ($sArtId) {
            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC);

            $sAttrViewName = getViewName('oxattribute');
            $sViewName = getViewName('oxobject2attribute');

            $sSelect = "select o2a.*, {$sAttrViewName}.* from $sViewName as o2a ";
            $sSelect .= "left join {$sAttrViewName} on {$sAttrViewName}.oxid = o2a.oxattrid ";
            $sSelect .= "where o2a.oxobjectid = :oxobjectid and {$sAttrViewName}.oxdisplayinbasket  = 1 and o2a.oxvalue != '' ";
            $sSelect .= "order by o2a.oxpos, {$sAttrViewName}.oxpos";

            $aAttributes = $oDb->getAll($sSelect, [
                ':oxobjectid' => $sArtId
            ]);

            if ($sParentId) {
                $aParentAttributes = $oDb->getAll($sSelect, [
                    ':oxobjectid' => $sParentId
                ]);
                $aAttributes = $this->_mergeAttributes($aAttributes, $aParentAttributes);
            }

            $this->assignArray($aAttributes);
        }
    }

    /**
     * get category attributes by category Id
     *
     * @param string  $sCategoryId category Id
     * @param integer $iLang       language No
     *
     * @return object;
     */
    public function getCategoryAttributes($sCategoryId, $iLang)
    {
        $aSessionFilter = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable('session_attrfilter');

        $oArtList = oxNew(\OxidEsales\Eshop\Application\Model\ArticleList::class);
        $oArtList->loadCategoryIDs($sCategoryId, $aSessionFilter);

        // Only if we have articles
        if (count($oArtList) > 0) {
            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            $sArtIds = '';
            foreach (array_keys($oArtList->getArray()) as $sId) {
                if ($sArtIds) {
                    $sArtIds .= ',';
                }
                $sArtIds .= $oDb->quote($sId);
            }

            $sAttTbl = getViewName('oxattribute', $iLang);
            $sO2ATbl = getViewName('oxobject2attribute', $iLang);
            $sC2ATbl = getViewName('oxcategory2attribute', $iLang);

            $sSelect = "SELECT DISTINCT att.oxid, att.oxtitle, o2a.oxvalue " .
                       "FROM $sAttTbl as att, $sO2ATbl as o2a ,$sC2ATbl as c2a " .
                       "WHERE att.oxid = o2a.oxattrid AND c2a.oxobjectid = :oxobjectid AND c2a.oxattrid = att.oxid AND o2a.oxvalue !='' AND o2a.oxobjectid IN ($sArtIds) " .
                       "ORDER BY c2a.oxsort , att.oxpos, att.oxtitle, o2a.oxvalue";

            $rs = $oDb->select($sSelect, [
                ':oxobjectid' => $sCategoryId
            ]);

            if ($rs != false && $rs->count() > 0) {
                while (!$rs->EOF && list($sAttId, $sAttTitle, $sAttValue) = $rs->fields) {
                    if (!$this->offsetExists($sAttId)) {
                        $oAttribute = oxNew(\OxidEsales\Eshop\Application\Model\Attribute::class);
                        $oAttribute->setTitle($sAttTitle);

                        $this->offsetSet($sAttId, $oAttribute);
                        $iLang = \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage();
                        if (isset($aSessionFilter[$sCategoryId][$iLang][$sAttId])) {
                            $oAttribute->setActiveValue($aSessionFilter[$sCategoryId][$iLang][$sAttId]);
                        }
                    } else {
                        $oAttribute = $this->offsetGet($sAttId);
                    }

                    $oAttribute->addValue($sAttValue);
                    $rs->fetchRow();
                }
            }
        }

        return $this;
    }

    /**
     * Merge attribute arrays
     *
     * @param array $aAttributes       array of attributes
     * @param array $aParentAttributes array of parent article attributes
     *
     * @return array $aAttributes
     */
    protected function _mergeAttributes($aAttributes, $aParentAttributes)
    {
        if (count($aParentAttributes)) {
            $aAttrIds = [];
            foreach ($aAttributes as $aAttribute) {
                $aAttrIds[] = $aAttribute['OXID'];
            }

            foreach ($aParentAttributes as $aAttribute) {
                if (!in_array($aAttribute['OXID'], $aAttrIds)) {
                    $aAttributes[] = $aAttribute;
                }
            }
        }

        return $aAttributes;
    }
}
