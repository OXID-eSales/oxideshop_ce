<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   tests
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id$
 */

require_once 'OxidTestCase.php';

/**
 * Test deployment if shop versions are deployed correctly.
 */
class Unit_deployTest extends OxidTestCase
{
    protected $_sThisDir = null;

    protected $_blSvnDirExists = null;

    static protected $_blTagsFound = null;

    static protected $_blEndifFound = null;

    protected $_blIsDeployed = null;

    protected $_aSuffixedfiles = array(
        "setup/sql",
        "out/pictures",
        "admin/menu.xml",
        "out/admin/img/login.png",
        "out/admin/src/colors.css",
        "out/admin/src/style.css",
        "out/basic/img/logo.png",
        //"out/basic/src/gui/theme.xml",
        "out/basic/src/oxid.css",
        "setup/de/lizenz.txt",
        "setup/en/lizenz.txt");

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        if (!$this->_sThisDir)
            $this->_sThisDir = getcwd();
        chdir(getShopBasePath());
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        chdir($this->_sThisDir);

        return parent::tearDown();
    }

    /**
     * return if asked is the current version
     *
     * @param string $sVersion shop version
     *
     * @return bool
     */
    protected function _isCurrentVersion($sVersion)
    {
        $sVer = getenv('OXID_VERSION');
        if ($sVer) {
            return strtoupper($sVer) == strtoupper($sVersion);
        }
        if (defined('OXID_VERSION_EE') && OXID_VERSION_EE) {
            return strtoupper($sVersion) == 'EE';
        }
        if (defined('OXID_VERSION_PE_PE') && OXID_VERSION_PE_PE) {
            return strtoupper($sVersion) == 'PE';
        }
        if (defined('OXID_VERSION_PE_CE') && OXID_VERSION_PE_CE) {
            return strtoupper($sVersion) == 'CE';
        }
        throw new Exception('could not determine version');
    }

    /**
     * Check if .svn directories exist.
     *
     * @return boolean
     */
    protected function _svnDirExists()
    {
        if (!is_null($this->_blSvnDirExists))
            return $this->_blSvnDirExists;

        $sOut = exec('find '.escapeshellarg(getShopBasePath()).' -iname ".svn" -type d', $aOut);

        $this->_blSvnDirExists = false;
        if (count($aOut) > 0) {
            $this->_blSvnDirExists = implode("\n", $aOut);
        }
        return $this->_blSvnDirExists;
    }

    /**
     * Check if version tags exists.
     *
     * @return boolean
     */
    protected function _versionTagsExists()
    {
        if (!is_null(self::$_blTagsFound))
            return self::$_blTagsFound;

