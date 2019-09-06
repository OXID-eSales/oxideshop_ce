<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxDb;
use oxRegistry;
use oxField;

/**
 * Article attributes manager.
 * Collects and keeps attributes of chosen article.
 *
 */
class Attribute extends \OxidEsales\Eshop\Core\Model\MultiLanguageModel
{
    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxattribute';

    /**
     * Selected attribute value
     *
     * @var string
     */
    protected $_sActiveValue = null;

    /**
     * Attribute title
     *
     * @var string
     */
    protected $_sTitle = null;

    /**
     * Attribute values
     *
     * @var array
     */
    protected $_aValues = null;

    /**
     * Class constructor, initiates parent constructor (parent::oxBase()).
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('oxattribute');
    }

    /**
     * Removes attributes from articles, returns true on success.
     *
     * @param string $sOXID Object ID
     *
     * @return bool
     */
    public function delete($sOXID = null)
    {
        if (!$sOXID) {
            $sOXID = $this->getId();
        }

        if (!$this->canDeleteAttribute($sOXID)) {
            return false;
        }

        // remove attributes from articles also
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sDelete = "delete from oxobject2attribute where oxattrid = :oxattrid";
        $oDb->execute($sDelete, [
            ':oxattrid' => $sOXID
        ]);

        // #657 ADDITIONAL removes attribute connection to category
        $sDelete = "delete from oxcategory2attribute where oxattrid = :oxattrid";
        $oDb->execute($sDelete, [
            ':oxattrid' => $sOXID
        ]);

        return parent::delete($sOXID);
    }

    /**
     * Assigns attribute to variant
     *
     * @param array $aMDVariants article ids with selectionlist values
     * @param array $aSelTitle   selection list titles
     */
    public function assignVarToAttribute($aMDVariants, $aSelTitle)
    {
        $myLang = \OxidEsales\Eshop\Core\Registry::getLang();
        $aConfLanguages = $myLang->getLanguageIds();
        $sAttrId = $this->_getAttrId($aSelTitle[0]);
        if (!$sAttrId) {
            $sAttrId = $this->_createAttribute($aSelTitle);
        }
        foreach ($aMDVariants as $sVarId => $oValue) {
            if (strpos($sVarId, "mdvar_") === 0) {
                foreach ($oValue as $sId) {
                    $sVarId = substr($sVarId, 6);
                    $oNewAssign = oxNew(\OxidEsales\Eshop\Core\Model\BaseModel::class);
                    $oNewAssign->init("oxobject2attribute");
                    $sNewId = \OxidEsales\Eshop\Core\Registry::getUtilsObject()->generateUID();
                    if ($oNewAssign->load($sId)) {
                        $oNewAssign->oxobject2attribute__oxobjectid = new \OxidEsales\Eshop\Core\Field($sVarId);
                        $oNewAssign->setId($sNewId);
                        $oNewAssign->save();
                    }
                }
            } else {
                $oNewAssign = oxNew(\OxidEsales\Eshop\Core\Model\MultiLanguageModel::class);
                $oNewAssign->setEnableMultilang(false);
                $oNewAssign->init("oxobject2attribute");
                $oNewAssign->oxobject2attribute__oxobjectid = new \OxidEsales\Eshop\Core\Field($sVarId);
                $oNewAssign->oxobject2attribute__oxattrid = new \OxidEsales\Eshop\Core\Field($sAttrId);
                foreach ($aConfLanguages as $sKey => $sLang) {
                    $sPrefix = $myLang->getLanguageTag($sKey);
                    $oNewAssign->{'oxobject2attribute__oxvalue' . $sPrefix} = new \OxidEsales\Eshop\Core\Field($oValue[$sKey]->name);
                }
                $oNewAssign->save();
            }
        }
    }

    /**
     * Searches for attribute by oxtitle. If exists returns attribute id
     *
     * @param string $sSelTitle selection list title
     *
     * @return mixed attribute id or false
     */
    protected function _getAttrId($sSelTitle)
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDB();
        $sAttViewName = getViewName('oxattribute');

        return $oDb->getOne("select oxid from $sAttViewName where LOWER(oxtitle) = :oxtitle ", [
            ':oxtitle' => getStr()->strtolower($sSelTitle)
        ]);
    }

    /**
     * Checks if attribute exists
     *
     * @param array $aSelTitle selection list title
     *
     * @return string attribute id
     */
    protected function _createAttribute($aSelTitle)
    {
        $myLang = \OxidEsales\Eshop\Core\Registry::getLang();
        $aConfLanguages = $myLang->getLanguageIds();
        $oAttr = oxNew(\OxidEsales\Eshop\Core\Model\MultiLanguageModel::class);
        $oAttr->setEnableMultilang(false);
        $oAttr->init('oxattribute');
        foreach ($aConfLanguages as $sKey => $sLang) {
            $sPrefix = $myLang->getLanguageTag($sKey);
            $oAttr->{'oxattribute__oxtitle' . $sPrefix} = new \OxidEsales\Eshop\Core\Field($aSelTitle[$sKey]);
        }
        $oAttr->save();

        return $oAttr->getId();
    }

    /**
     * Returns all oxobject2attribute Ids of article
     *
     * @param string $sArtId article ids
     *
     * @return null;
     */
    public function getAttributeAssigns($sArtId)
    {
        if ($sArtId) {
            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

            $sSelect = "select o2a.oxid from oxobject2attribute as o2a ";
            $sSelect .= "where o2a.oxobjectid = :oxobjectid order by o2a.oxpos";

            $aIds = [];
            $rs = $oDb->select($sSelect, [
                ':oxobjectid' => $sArtId
            ]);
            if ($rs != false && $rs->count() > 0) {
                while (!$rs->EOF) {
                    $aIds[] = $rs->fields[0];
                    $rs->fetchRow();
                }
            }

            return $aIds;
        }
    }


    /**
     * Set attribute title
     *
     * @param string $sTitle - attribute title
     */
    public function setTitle($sTitle)
    {
        $this->_sTitle = getStr()->htmlspecialchars($sTitle);
    }

    /**
     * Get attribute Title
     *
     * @return String
     */
    public function getTitle()
    {
        return $this->_sTitle;
    }

    /**
     * Add attribute value
     *
     * @param string $sValue - attribute value
     */
    public function addValue($sValue)
    {
        $this->_aValues[] = getStr()->htmlspecialchars($sValue);
    }

    /**
     * Set attribute selected value
     *
     * @param string $sValue - attribute value
     */
    public function setActiveValue($sValue)
    {
        $this->_sActiveValue = getStr()->htmlspecialchars($sValue);
    }

    /**
     * Get attribute Selected value
     *
     * @return String
     */
    public function getActiveValue()
    {
        return $this->_sActiveValue;
    }

    /**
     * Get attribute values
     *
     * @return Array
     */
    public function getValues()
    {
        return $this->_aValues;
    }

    /**
     * Checks if possible to delete attribute.
     *
     * @param string $oxId
     *
     * @return bool
     */
    protected function canDeleteAttribute($oxId)
    {
        $canDelete = true;
        if (!$oxId) {
            $canDelete = false;
        }

        return $canDelete;
    }
}
