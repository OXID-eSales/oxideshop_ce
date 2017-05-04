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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

if (!getenv('TRAVIS_ERROR_LEVEL')) {
    // DO NOT TOUCH THIS _ INSTEAD FIX NOTICES - DODGER
    error_reporting((E_ALL ^ E_NOTICE) | E_STRICT);
    ini_set('display_errors', true);
}

define ('OXID_PHP_UNIT', true);

$_sOverridenShopBasePath = null;

/**
 * Sets a path to the test shop
 *
 * @deprecated Define OX_BASE_PATH constant instead
 *
 * @param string $sPath New path to shop
 */
function overrideGetShopBasePath($sPath)
{
    //TS2012-06-06
    die("overrideGetShopBasePath() is deprecated use OX_BASE_PATH constant instead. ALWAYS.");
    global $_sOverridenShopBasePath;
    $_sOverridenShopBasePath = $sPath;
}

define('OX_BASE_PATH', isset($_sOverridenShopBasePath) ? $_sOverridenShopBasePath : oxPATH);

function getTestsBasePath()
{
    return realpath(dirname(__FILE__) . '/..').'/';
}

require_once 'test_utils.php';

// Generic utility method file.
require_once OX_BASE_PATH . 'core/oxfunctions.php';

// As in new bootstrap to get db instance.
$oConfigFile = new OxConfigFile(OX_BASE_PATH . "config.inc.php");
OxRegistry::set("OxConfigFile", $oConfigFile);
oxRegistry::set("oxConfig", new oxConfig());
if ($sTestType == 'acceptance') {
    oxRegistry::set("oxConfig", oxNew('oxConfig'));
}

// As in new bootstrap to get db instance.
$oDb = new oxDb();
$oDb->setConfig($oConfigFile);
$oLegacyDb = $oDb->getDb();
OxRegistry::set('OxDb', $oLegacyDb);

oxRegistry::getConfig();

/**
 * Useful for defining custom time
 */
class modOxUtilsDate extends oxUtilsDate
{

    protected $_sTime = null;

    public static function getInstance()
    {
        return oxRegistry::get("oxUtilsDate");
    }

    public function UNITSetTime($sTime)
    {
        $this->_sTime = $sTime;
    }

    public function getTime()
    {
        if (!is_null($this->_sTime)) {
            return $this->_sTime;
        }

        return parent::getTime();
    }
}

// Utility class
require_once getShopBasePath() . 'core/oxutils.php';

// Database managing class.
require_once getShopBasePath() . 'core/adodblite/adodb.inc.php';

// Session managing class.
require_once getShopBasePath() . 'core/oxsession.php';

// Database session managing class.
// included in session file if needed - require_once( getShopBasePath() . 'core/adodb/session/adodb-session.php');

// DB managing class.
//require_once( getShopBasePath() . 'core/adodb/drivers/adodb-mysql.inc.php');
require_once getShopBasePath() . 'core/oxconfig.php';
