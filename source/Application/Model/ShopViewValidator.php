<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxDb;

/**
 * Shop view validator.
 * checks which views are valid / invalid
 *
 */
class ShopViewValidator
{
    protected $_aMultiLangTables = [];

    protected $_aMultiShopTables = [];

    protected $_aLanguages = [];

    protected $_aAllShopLanguages = [];

    protected $_iShopId = null;

    protected $_aAllViews = [];

    protected $_aShopViews = [];

    protected $_aValidShopViews = [];

    /**
     * Sets multi language tables.
     *
     * @param null $aMultiLangTables
     */
    public function setMultiLangTables($aMultiLangTables)
    {
        $this->_aMultiLangTables = $aMultiLangTables;
    }

    /**
     * Returns multi lang tables
     *
     * @return array
     */
    public function getMultiLangTables()
    {
        return $this->_aMultiLangTables;
    }


    /**
     * Sets multi shop tables.
     *
     * @param array $aMultiShopTables
     */
    public function setMultiShopTables($aMultiShopTables)
    {
        $this->_aMultiShopTables = $aMultiShopTables;
    }

    /**
     * Returns multi shop tables
     *
     * @return array
     */
    public function getMultiShopTables()
    {
        return $this->_aMultiShopTables;
    }

    /**
     * Returns list of active languages in shop
     *
     * @param array $aLanguages
     */
    public function setLanguages($aLanguages)
    {
        $this->_aLanguages = $aLanguages;
    }

    /**
     * Gets languages.
     *
     * @return array
     */
    public function getLanguages()
    {
        return $this->_aLanguages;
    }

    /**
     * Returns list of active languages in shop
     *
     * @param array $aAllShopLanguages
     */
    public function setAllShopLanguages($aAllShopLanguages)
    {
        $this->_aAllShopLanguages = $aAllShopLanguages;
    }

    /**
     * Gets all shop languages.
     *
     * @return array
     */
    public function getAllShopLanguages()
    {
        return $this->_aAllShopLanguages;
    }


    /**
     * Sets shop id.
     *
     * @param integer $iShopId
     */
    public function setShopId($iShopId)
    {
        $this->_iShopId = $iShopId;
    }

    /**
     * Returns list of available shops
     *
     * @return integer
     */
    public function getShopId()
    {
        return $this->_iShopId;
    }

    /**
     * Returns list of all shop views
     *
     * @return array
     */
    protected function _getAllViews()
    {
        if (empty($this->_aAllViews)) {
            $this->_aAllViews = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getCol("SHOW TABLES LIKE  'oxv\_%'");
        }

        return $this->_aAllViews;
    }

    /**
     * Checks if given view name belongs to current subshop or is general view
     *
     * @param string $sViewName View name
     *
     * @return bool
     */
    protected function _isCurrentShopView($sViewName)
    {
        $blResult = false;

        $blEndsWithShopId = preg_match("/[_]([0-9]+)$/", $sViewName, $aMatchEndsWithShopId);
        $blContainsShopId = preg_match("/[_]([0-9]+)[_]/", $sViewName, $aMatchContainsShopId);

        if ((!$blEndsWithShopId && !$blContainsShopId) ||
            ($blEndsWithShopId && $aMatchEndsWithShopId[1] == $this->getShopId()) ||
            ($blContainsShopId && $aMatchContainsShopId[1] == $this->getShopId())
        ) {
            $blResult = true;
        }

        return $blResult;
    }


    /**
     * Returns list of shop specific views currently in database
     *
     * @return array
     */
    protected function _getShopViews()
    {
        if (empty($this->_aShopViews)) {
            $this->_aShopViews = [];
            $aAllViews = $this->_getAllViews();

            foreach ($aAllViews as $sView) {
                if ($this->_isCurrentShopView($sView)) {
                    $this->_aShopViews[] = $sView;
                }
            }
        }

        return $this->_aShopViews;
    }

    /**
     * Returns list of valid shop views
     *
     * @return array
     */
    protected function _getValidShopViews()
    {
        if (empty($this->_aValidShopViews)) {
            $aTables = $this->getShopTables();
            $this->_aValidShopViews = [];

            foreach ($aTables as $sTable) {
                $this->prepareShopTableViewNames($sTable);
            }
        }

        return $this->_aValidShopViews;
    }

    /**
     * Get list of shop tables
     *
     * @return array
     */
    protected function getShopTables()
    {
        $shopTables = $this->getMultilangTables();

        return $shopTables;
    }

    /**
     * Appends possible table views to $this->_aValidShopViews variable.
     *
     * @param string $tableName
     */
    protected function prepareShopTableViewNames($tableName)
    {
        $this->_aValidShopViews[] = 'oxv_' . $tableName;

        if (in_array($tableName, $this->getMultiLangTables())) {
            foreach ($this->getAllShopLanguages() as $sLang) {
                $this->_aValidShopViews[] = 'oxv_' . $tableName . '_' . $sLang;
            }
        }
    }

    /**
     * Checks if view name is valid according to current config
     *
     * @param string $sViewName View name
     *
     * @return bool
     */
    protected function _isViewValid($sViewName)
    {
        return in_array($sViewName, $this->_getValidShopViews());
    }

    /**
     * Returns list of invalid views
     *
     * @return array
     */
    public function getInvalidViews()
    {
        $aInvalidViews = [];
        $aShopViews = $this->_getShopViews();

        foreach ($aShopViews as $sView) {
            if (!$this->_isViewValid($sView)) {
                $aInvalidViews[] = $sView;
            }
        }

        return $aInvalidViews;
    }
}
