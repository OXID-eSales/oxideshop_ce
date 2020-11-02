<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

/**
 * Content list manager.
 * Collects list of content.
 */
class ContentList extends \OxidEsales\Eshop\Core\Model\ListModel
{
    /**
     * Information content type.
     *
     * @var int
     */
    public const TYPE_INFORMATION_CONTENTS = 0;

    /**
     * Main menu list type.
     *
     * @var int
     */
    public const TYPE_MAIN_MENU_LIST = 1;

    /**
     * Main menu list type.
     *
     * @var int
     */
    public const TYPE_CATEGORY_MENU = 2;

    /**
     * Service list.
     *
     * @var int
     */
    public const TYPE_SERVICE_LIST = 3;

    /**
     * List of services.
     *
     * @var array
     */
    protected $_aServiceKeys = ['oximpressum', 'oxagb', 'oxsecurityinfo', 'oxdeliveryinfo', 'oxrightofwithdrawal', 'oxorderinfo', 'oxcredits'];

    /**
     * Sets service keys.
     *
     * @param array $aServiceKeys
     */
    public function setServiceKeys($aServiceKeys): void
    {
        $this->_aServiceKeys = $aServiceKeys;
    }

    /**
     * Gets services keys.
     *
     * @return array
     */
    public function getServiceKeys()
    {
        return $this->_aServiceKeys;
    }

    /**
     * Class constructor, initiates parent constructor (parent::oxList()).
     */
    public function __construct()
    {
        parent::__construct('oxcontent');
    }

    /**
     * Loads main menue entries and generates list with links.
     */
    public function loadMainMenulist(): void
    {
        $this->_load(self::TYPE_MAIN_MENU_LIST);
    }

    /**
     * Load Array of Menue items and change keys of aList to catid.
     */
    public function loadCatMenues(): void
    {
        $this->_load(self::TYPE_CATEGORY_MENU);
        $aArray = [];

        if ($this->count()) {
            foreach ($this as $oContent) {
                // add into category tree
                if (!isset($aArray[$oContent->getCategoryId()])) {
                    $aArray[$oContent->getCategoryId()] = [];
                }

                $aArray[$oContent->oxcontents__oxcatid->value][] = $oContent;
            }
        }

        $this->_aArray = $aArray;
    }

    /**
     * Get data from db.
     *
     * @param int $iType - type of content
     *
     * @return array
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "loadFromDb" in next major
     */
    protected function _loadFromDb($iType) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $sSql = $this->_getSQLByType($iType);

        return \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC)->getAll($sSql);
    }

    /**
     * Load category list data.
     *
     * @param int $type - type of content
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "load" in next major
     */
    protected function _load($type): void // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $data = $this->_loadFromDb($type);
        $this->assignArray($data);
    }

    /**
     * Load category list data.
     */
    public function loadServices(): void
    {
        $this->_load(self::TYPE_SERVICE_LIST);
        $this->_extractListToArray();
    }

    /**
     * Extract oxContentList object to associative array with oxloadid as keys.
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "extractListToArray" in next major
     */
    protected function _extractListToArray(): void // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $aExtractedContents = [];
        foreach ($this as $oContent) {
            $aExtractedContents[$oContent->getLoadId()] = $oContent;
        }

        $this->_aArray = $aExtractedContents;
    }

    /**
     * Creates SQL by type.
     *
     * @param int $iType type
     *
     * @return string
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "getSQLByType" in next major
     */
    protected function _getSQLByType($iType) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $sSQLAdd = '';
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sSQLType = ' AND `oxtype` = ' . $oDb->quote($iType);

        if (self::TYPE_CATEGORY_MENU === $iType) {
            $sSQLAdd = " AND `oxcatid` IS NOT NULL AND `oxsnippet` = '0'";
        }

        if (self::TYPE_SERVICE_LIST === $iType) {
            $sIdents = implode(', ', \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray($this->getServiceKeys()));
            $sSQLAdd = ' AND OXLOADID IN (' . $sIdents . ')';
            $sSQLType = '';
        }
        $sViewName = $this->getBaseObject()->getViewName();

        return "SELECT * FROM {$sViewName} WHERE `oxactive` = '1' $sSQLType AND `oxshopid` = " . $oDb->quote($this->_sShopID) . " $sSQLAdd ORDER BY `oxloadid`";
    }
}
