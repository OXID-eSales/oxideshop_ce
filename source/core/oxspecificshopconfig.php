<?php
/**
 * This file is part of OXID Console.
 *
 * OXID Console is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID Console is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID Console.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author    OXID Professional services
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 */

/**
 * Specific shop config class
 *
 * Helper class for generating oxConfig instance for specific shop
 */
class oxSpecificShopConfig extends oxConfig
{

    /**
     * @var int
     */
    protected $_iShopId;

    /**
     * Constructor
     *
     * @param $iShopId
     */
    public function __construct($iShopId)
    {
        $this->_iShopId = $iShopId;
        $this->init();
    }

    /**
     * Returns config arrays for all shops
     *
     * @return oxSpecificShopConfig[]
     */
    public static function getAll()
    {
        $aShopIds = oxDb::getDb()->getCol('SELECT oxid FROM oxshops');
        $aConfigs = array();

        foreach ($aShopIds as $mShopId) {
            // Note: not using static::get() for avoiding checking of is shop id valid
            $aConfigs[] = new oxSpecificShopConfig($mShopId);
        }

        return $aConfigs;
    }

    /**
     * Get config object of given shop id
     *
     * @param string|integer $mShopId
     *
     * @return oxSpecificShopConfig|null
     */
    public static function get($mShopId)
    {
        $sSQL = 'SELECT 1 FROM oxshops WHERE oxid = %s';
        $oDb = oxDb::getDb();

        if (!$oDb->getOne(sprintf($sSQL, $oDb->quote($mShopId)))) { // invalid shop id
            // Not using oxConfig::_isValidShopId() because its not static, but YES it should be
            return null;
        }

        return new oxSpecificShopConfig($mShopId);
    }

    /**
     * {@inheritdoc}
     *
     * @return null|void
     */
    public function init()
    {
        // Duplicated init protection
        if ($this->_blInit) {
            return;
        }
        $this->_blInit = true;

        $this->_loadVarsFromFile();
        include getShopBasePath() . 'core/oxconfk.php';

        $this->_setDefaults();

        try {
            $sShopID = $this->getShopId();
            $blConfigLoaded = $this->_loadVarsFromDb($sShopID);

            // loading shop config
            if (empty($sShopID) || !$blConfigLoaded) {
                /** @var oxConnectionException $oEx */
                $oEx = oxNew("oxConnectionException");
                $oEx->setMessage("Unable to load shop config values from database");
                throw $oEx;
            }

            // loading theme config options
            $this->_loadVarsFromDb(
                $sShopID, null, oxConfig::OXMODULE_THEME_PREFIX . $this->getConfigParam('sTheme')
            );

            // checking if custom theme (which has defined parent theme) config options should be loaded over parent theme (#3362)
            if ($this->getConfigParam('sCustomTheme')) {
                $this->_loadVarsFromDb(
                    $sShopID, null, oxConfig::OXMODULE_THEME_PREFIX . $this->getConfigParam('sCustomTheme')
                );
            }

            // loading modules config
            $this->_loadVarsFromDb($sShopID, null, oxConfig::OXMODULE_MODULE_PREFIX);

            $aOnlyMainShopVars = array('blMallUsers', 'aSerials', 'IMD', 'IMA', 'IMS');
            $this->_loadVarsFromDb($this->getBaseShopId(), $aOnlyMainShopVars);
        } catch (oxConnectionException $oEx) {
            $oEx->debugOut();
            oxRegistry::getUtils()->showMessageAndExit($oEx->getString());
        }
    }

    /**
     * Get shop id
     *
     * @return int
     */
    public function getShopId()
    {
        return $this->_iShopId;
    }
}