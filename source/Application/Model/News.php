<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxRegistry;
use oxDb;
use oxField;
use oxUtilsView;

/**
 * News manager.
 * Performs news text collection. News may be sorted by user categories (only
 * these user may read news), etc.
 *
 * @deprecated since v.5.3.0 (2016-06-17); The Admin Menu: Customer Info -> News feature will be moved to a module in v6.0.0
 *
 */
class News extends \OxidEsales\Eshop\Core\Model\MultiLanguageModel
{
    /**
     * User group object (default null).
     *
     * @var object
     */
    protected $_oGroups = null;

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxnews';

    /**
     * Class constructor, initiates parent constructor (parent::oxI18n()).
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('oxnews');
    }

    /**
     * Assigns object data.
     *
     * @param string $dbRecord database record to be assigned
     */
    public function assign($dbRecord)
    {
        parent::assign($dbRecord);

        // convert date's to international format
        if ($this->oxnews__oxdate) {
            $this->oxnews__oxdate->setValue(\OxidEsales\Eshop\Core\Registry::getUtilsDate()->formatDBDate($this->oxnews__oxdate->value));
        }
    }

    /**
     * Returns list of user groups assigned to current news object
     *
     * @return oxlist
     */
    public function getGroups()
    {
        if ($this->_oGroups == null && $sOxid = $this->getId()) {
            // usergroups
            $this->_oGroups = oxNew('oxlist', 'oxgroups');
            $sViewName = getViewName("oxgroups", $this->getLanguage());
            $sSelect = "select {$sViewName}.* from {$sViewName}, oxobject2group ";
            $sSelect .= "where oxobject2group.oxobjectid = :oxobjectid ";
            $sSelect .= "and oxobject2group.oxgroupsid={$sViewName}.oxid ";
            $this->_oGroups->selectString($sSelect, [
                ':oxobjectid' => $sOxid
            ]);
        }

        return $this->_oGroups;
    }

    /**
     * Checks if this object is in group, returns true on success.
     *
     * @param string $sGroupID user group ID
     *
     * @return bool
     */
    public function inGroup($sGroupID)
    {
        $blResult = false;
        $aGroups = $this->getGroups();
        foreach ($aGroups as $oObject) {
            if ($oObject->_sOXID == $sGroupID) {
                $blResult = true;
                break;
            }
        }

        return $blResult;
    }

    /**
     * Deletes object information from DB, returns true on success.
     *
     * @param string $sOxid Object ID (default null)
     *
     * @return bool
     */
    public function delete($sOxid = null)
    {
        if (!$sOxid) {
            $sOxid = $this->getId();
        }
        if (!$sOxid) {
            return false;
        }

        if ($blDelete = parent::delete($sOxid)) {
            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            $oDb->execute("delete from oxobject2group where oxobject2group.oxobjectid = :oxobjectid", [
                ':oxobjectid' => $sOxid
            ]);
        }

        return $blDelete;
    }

    /**
     * Updates object information in DB.
     */
    protected function _update()
    {
        $this->oxnews__oxdate->setValue(\OxidEsales\Eshop\Core\Registry::getUtilsDate()->formatDBDate($this->oxnews__oxdate->value, true));

        parent::_update();
    }

    /**
     * Inserts object details to DB, returns true on success.
     *
     * @return bool
     */
    protected function _insert()
    {
        if (!$this->oxnews__oxdate || \OxidEsales\Eshop\Core\Registry::getUtilsDate()->isEmptyDate($this->oxnews__oxdate->value)) {
            // if date field is empty, assigning current date
            $this->oxnews__oxdate = new \OxidEsales\Eshop\Core\Field(date('Y-m-d'));
        } else {
            $this->oxnews__oxdate = new \OxidEsales\Eshop\Core\Field(\OxidEsales\Eshop\Core\Registry::getUtilsDate()->formatDBDate($this->oxnews__oxdate->value, true));
        }

        return parent::_insert();
    }

    /**
     * Sets data field value
     *
     * @param string $sFieldName index OR name (eg. 'oxarticles__oxtitle') of a data field to set
     * @param string $sValue     value of data field
     * @param int    $iDataType  field type
     *
     * @return null
     */
    protected function _setFieldData($sFieldName, $sValue, $iDataType = \OxidEsales\Eshop\Core\Field::T_TEXT)
    {
        switch (strtolower($sFieldName)) {
            case 'oxlongdesc':
            case 'oxnews__oxlongdesc':
                $iDataType = \OxidEsales\Eshop\Core\Field::T_RAW;
                break;
        }
        return parent::_setFieldData($sFieldName, $sValue, $iDataType);
    }

    /**
     * get long description, parsed through smarty
     *
     * @return string
     */
    public function getLongDesc()
    {
        /** @var \OxidEsales\Eshop\Core\UtilsView $oUtilsView */
        $oUtilsView = \OxidEsales\Eshop\Core\Registry::getUtilsView();
        return $oUtilsView->parseThroughSmarty($this->oxnews__oxlongdesc->getRawValue(), $this->getId() . $this->getLanguage(), null, true);
    }
}
