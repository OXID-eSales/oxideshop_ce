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

if (!defined('SHOP_PATH')) {
    define('SHOP_PATH', dirname(__FILE__) . '/../../');
}

/**
 * Class for shop installation.
 */
class ShopInstaller
{
    /** @var resource  */
    private $_oDb = null;

    /** @var string Shop setup directory path */
    private $_sSetupDirectory = null;

    /**
     * Includes configuration files.
     */
    public function __construct()
    {
        include SHOP_PATH . "config.inc.php";
        include SHOP_PATH . "core/oxconfk.php";

        if (!defined('OXID_VERSION_SUFIX')) {
            define('OXID_VERSION_SUFIX', '');
        }
    }

    /**
     * Sets shop setup directory.
     *
     * @param string $sSetupPath Path to setup files to use instead of shop ones.
     */
    public function setSetupDirectory($sSetupPath)
    {
        $this->_sSetupDirectory = $sSetupPath;
    }

    /**
     * Deletes browser cookies.
     *
     * @return array
     */
    public function deleteCookies()
    {
        $aDeletedCookies = array();
        if (isset($_SERVER['HTTP_COOKIE'])) {
            $aCookies = explode(';', $_SERVER['HTTP_COOKIE']);
            foreach ($aCookies as $sCookie) {
                $sRawCookie = explode('=', $sCookie);
                setcookie(trim($sRawCookie[0]), '', time() - 10000, '/');
                $aDeletedCookies[] = $sRawCookie[0];
            }
        }
        return $aDeletedCookies;
    }

    /**
     * Clears temp directory.
     */
    public function clearTemp()
    {
        $this->delTree($this->sCompileDir, false);
    }

    /**
     * Sets up database.
     */
    public function setupDatabase()
    {
        if ($this->getCharsetMode() == 'utf8') {
            $this->query("alter schema character set utf8 collate utf8_general_ci");
            $this->query("set names 'utf8'");
            $this->query("set character_set_database=utf8");
            $this->query("set character set latin1");//mysql_query("set character set utf8",$oDB);
            $this->query("set character_set_connection = utf8");
            $this->query("set character_set_results = utf8");
            $this->query("set character_set_server = utf8");
        } else {
            $this->query("alter schema character set latin1 collate latin1_general_ci");
            $this->query("set character set latin1");
        }

        $this->query('drop database `' . $this->dbName . '`');
        $this->query('create database `' . $this->dbName . '` collate ' . $this->getCharsetMode() . '_general_ci');

        $sSetupPath = $this->getSetupDirectory();
        $this->importFileToDatabase($sSetupPath . '/sql' . OXID_VERSION_SUFIX . '/' . 'database_schema.sql');
        $this->importFileToDatabase($sSetupPath . '/sql' . OXID_VERSION_SUFIX . '/' . 'initial_data.sql');
    }

    /**
     * Inserts demo data to shop.
     */
    public function insertDemoData()
    {
        $sSetupPath = $this->getSetupDirectory();
        $this->importFileToDatabase($sSetupPath . '/sql' . OXID_VERSION_SUFIX . '/' . 'test_demodata.sql');
    }

    /**
     * Convert shop to international.
     */
    public function convertToInternational()
    {
        $sSetupPath = $this->getSetupDirectory();
        $this->importFileToDatabase($sSetupPath . '/sql' . OXID_VERSION_SUFIX . '/' . 'en.sql');
    }

    /**
     * Inserts missing configuration parameters
     */
    public function setConfigurationParameters()
    {
        $sShopId = $this->getShopId();

        $this->query("delete from oxconfig where oxvarname in ('iSetUtfMode','blLoadDynContents','sShopCountry');");
        $this->query(
            "insert into oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue) values " .
            "('config1', '{$sShopId}', 'iSetUtfMode',       'str',  ENCODE('0', '{$this->sConfigKey}') )," .
            "('config2', '{$sShopId}', 'blLoadDynContents', 'bool', ENCODE('1', '{$this->sConfigKey}') )," .
            "('config3', '{$sShopId}', 'sShopCountry',      'str',  ENCODE('de','{$this->sConfigKey}') )"
        );
    }

    /**
     * Adds serial number to shop.
     *
     * @param string $sSerial
     */
    public function setSerialNumber($sSerial)
    {
        $sShopId = $this->getShopId();

        include_once SHOP_PATH . "core/oxserial.php";

        $oSerial = new oxSerial();
        $oSerial->setEd($this->getShopEdition());
        $oSerial->isValidSerial($sSerial);

        $iMaxDays = $oSerial->getMaxDays($sSerial);
        $iMaxArticles = $oSerial->getMaxArticles($sSerial);
        $iMaxShops = $oSerial->getMaxShops($sSerial);

        $this->query("update oxshops set oxserial = '{$sSerial}'");
        $this->query("delete from oxconfig where oxvarname in ('aSerials','sTagList','IMD','IMA','IMS')");
        $this->query(
            "insert into oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue) values " .
            "('serial1', '{$sShopId}', 'aSerials', 'arr', ENCODE('" . serialize(array($sSerial)) . "','{$this->sConfigKey}') )," .
            "('serial2', '{$sShopId}', 'sTagList', 'str', ENCODE('" . time() . "','{$this->sConfigKey}') )," .
            "('serial3', '{$sShopId}', 'IMD',      'str', ENCODE('" . $iMaxDays . "','{$this->sConfigKey}') )," .
            "('serial4', '{$sShopId}', 'IMA',      'str', ENCODE('" . $iMaxArticles . "','{$this->sConfigKey}') )," .
            "('serial5', '{$sShopId}', 'IMS',      'str', ENCODE('" . $iMaxShops . "','{$this->sConfigKey}') )"
        );
    }

    /**
     * Converts shop to utf8.
     */
    public function convertToUtf()
    {
        $oDB = $this->getDb();

        $rs = $this->query(
            "SELECT oxvarname, oxvartype, DECODE( oxvarvalue, '{$this->sConfigKey}') AS oxvarvalue
                       FROM oxconfig
                       WHERE oxvartype IN ('str', 'arr', 'aarr')
                       #AND oxvarname != 'aCurrencies'
                       "
        );

        $aConverted = array();
        while ($aRow = mysql_fetch_assoc($rs)) {
            if ($aRow['oxvartype'] == 'arr' || $aRow['oxvartype'] == 'aarr') {
                $aRow['oxvarvalue'] = unserialize($aRow['oxvarvalue']);
            }
            $aRow['oxvarvalue'] = $this->stringToUtf($aRow['oxvarvalue']);
            $aConverted[] = $aRow;
        }

        foreach ($aConverted as $aConfigParam) {
            $sConfigName = $aConfigParam['oxvarname'];
            $sConfigValue = $aConfigParam['oxvarvalue'];
            if (is_array($sConfigValue)) {
                $sConfigValue = mysql_real_escape_string(serialize($sConfigValue), $oDB);
            } elseif (is_string($sConfigValue)) {
                $sConfigValue = mysql_real_escape_string($sConfigValue, $oDB);
            }

            $this->query("update oxconfig set oxvarvalue = ENCODE( '{$sConfigValue}','{$this->sConfigKey}') where oxvarname = '{$sConfigName}';");
        }

        // Change currencies value to same as after 4.6 setup because previous encoding break it.
        $this->query(
            "REPLACE INTO `oxconfig` (`OXID`, `OXSHOPID`, `OXMODULE`, `OXVARNAME`, `OXVARTYPE`, `OXVARVALUE`) VALUES
            ('3c4f033dfb8fd4fe692715dda19ecd28', 'oxbaseshop', '', 'aCurrencies', 'arr', 0x4dbace2972e14bf2cbd3a9a45157004422e928891572b281961cdebd1e0bbafe8b2444b15f2c7b1cfcbe6e5982d87434c3b19629dacd7728776b54d7caeace68b4b05c6ddeff2df9ff89b467b14df4dcc966c504477a9eaeadd5bdfa5195a97f46768ba236d658379ae6d371bfd53acd9902de08a1fd1eeab18779b191f3e31c258a87b58b9778f5636de2fab154fc0a51a2ecc3a4867db070f85852217e9d5e9aa60507);"
        );
    }

    /**
     * Turns varnish on.
     */
    public function turnVarnishOn()
    {
        $this->query("DELETE from oxconfig WHERE oxshopid = 1 AND oxvarname in ('iLayoutCacheLifeTime', 'blReverseProxyActive');");
        $this->query(
            "INSERT INTO oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue) VALUES
              ('35863f223f91930177693956aafe69e6', 1, 'iLayoutCacheLifeTime', 'str', 0xB00FB55D),
              ('dbcfca66eed01fd43963443d35b109e0', 1, 'blReverseProxyActive',  'bool', 0x07);"
        );
    }

    /**
     * Imports file data to database.
     *
     * @param string $sFile                   Path to file.
     * @param bool   $blSetDefaultCharsetMode Whether to change default charset of mysql when importing file.
     */
    public function importFileToDatabase($sFile, $blSetDefaultCharsetMode = false)
    {
        $oDB = $this->getDb();
        mysql_select_db($this->dbName, $oDB);

        $command = 'mysql -h' . $this->dbHost . ' -u' . $this->dbUser . ' -p' . $this->dbPwd . ' ' . $this->dbName;
        if ($blSetDefaultCharsetMode) {
            $command .= ' --default-character-set=' . $this->getCharsetMode();
        }
        $command .= ' < ' . "'$sFile'";

        passthru($command);
    }

    /**
     * Returns shop id.
     *
     * @return string
     */
    private function getShopId()
    {
        return 'oxbaseshop';
    }

    /**
     * Returns shop edition.
     *
     * @return int
     */
    private function getShopEdition()
    {
    }

    /**
     * Returns charset mode
     *
     * @return string
     */
    private function getCharsetMode()
    {
        return $this->iUtfMode ? 'utf8' : 'latin1';
    }

    /**
     * Returns database resource
     *
     * @return resource
     */
    private function getDb()
    {
        if (is_null($this->_oDb)) {
            $this->_oDb = mysql_connect($this->dbHost, $this->dbUser, $this->dbPwd);
        }

        return $this->_oDb;
    }

    /**
     * Executes query on database.
     *
     * @param string $sql Sql query to execute.
     *
     * @return resource
     */
    private function query($sql)
    {
        $oDB = $this->getDb();

        return mysql_query($sql, $oDB);
    }

    /**
     * Deletes directory tree.
     *
     * @param string $dir       Path to directory
     * @param bool   $rmBaseDir Whether to remove base directory
     */
    private function delTree($dir, $rmBaseDir = false)
    {
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->delTree("$dir/$file", true) : @unlink("$dir/$file");
        }
        if ($rmBaseDir) {
            @rmdir($dir);
        }
    }

    /**
     * Converts input string to utf8.
     *
     * @param string $input String for conversion.
     *
     * @return array|string
     */
    private function stringToUtf($input)
    {
        $output = array();
        if (is_array($input)) {
            foreach ($input as $key => $value) {
                $output[$this->stringToUtf($key)] = $this->stringToUtf($value);
            }
        } elseif (is_string($input)) {
            return iconv('iso-8859-15', 'utf-8', $input);
        } else {
            return $input;
        }
        return $output;
    }

    /**
     * Returns shop setup directory.
     *
     * @return string
     */
    protected function getSetupDirectory()
    {
        if ($this->_sSetupDirectory === null) {
            $this->_sSetupDirectory = SHOP_PATH . '/setup';
        }

        return $this->_sSetupDirectory;
    }
}
