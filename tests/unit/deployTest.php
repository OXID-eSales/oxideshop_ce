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
        "application/views/admin/menu.xml",
        "out/admin/img/login.png",
        "out/admin/src/colors.css",
        "out/admin/src/style.css",
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

        $this->_blSvnDirExists = false;

        $sOut = exec('find '.escapeshellarg(getShopBasePath()).' -iname ".svn" -type d', $aOut);
        if (count($aOut) > 0) {
            $this->_blSvnDirExists = implode("\n", $aOut);
        }

        if (!$this->_blSvnDirExists) {
            // Try to get svn info for svn 1.7.x
            $sOut = exec('svn info '.escapeshellarg(getShopBasePath()), $aOut);
            if (count($aOut) > 1) {
                $this->_blSvnDirExists = implode("\n", $aOut);
            }
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

        exec('grep -Rm1 "OXID'.'_VERSION\|DEPLOY'.'_REMOVE'.'_BEGIN\|DEPLOY'.'_REMOVE'.'_END\|endif *;" '.escapeshellarg(getShopBasePath()).' | grep -ve "wysiwigpro\|Smarty_Compiler\.class\.php\|core\.write_compiled_include\.php"', $aOut1);
        exec('grep -Rm1 "OXID'.'_VERSION\|DEPLOY'.'_REMOVE'.'_BEGIN\|DEPLOY'.'_REMOVE'.'_END\|endif *;" '.escapeshellarg(dirname(__FILE__)).' | grep -ve "deployerTestData\|deployTest\.php\|test_utils\.php"', $aOut2);

        self::$_blTagsFound = false;
        if (count($aOut1) > 0) {
            self::$_blTagsFound = $aOut1[0];
        }
        if (count($aOut2) > 0) {
            self::$_blTagsFound = $aOut2[0];
        }
        return self::$_blTagsFound;
    }

    /**
     * Checks version suffixes are removed from specific files and folders.
     *
     * If "sql" forldes exists instead of "sql_ce".
     *
     * @return null
     */
    protected function _nonSuffixesExists()
    {
        $sBasePath = getShopBasePath();
        $aRet = array();

        foreach ($this->_aSuffixedfiles as $sFile) {
            $sFullFile = $sBasePath . "/" . $sFile;
            if (!file_exists($sFullFile) && !is_dir($sFullFile)) {
                $aRet[] = $sFullFile;
            }
        }

        if (!count($aRet)) {
            return false;
        }
        return implode("\n", $aRet);
    }

    /**
     * Check if required files are ancoded.
     *
     * @return boolean
     */
    protected function _areFilesEncoded()
    {
        //because test are run on non encoded version
        $this->markTestSkipped('test are run on non encoded version');

        if ( $this->_isCurrentVersion('PE') || $this->_isCurrentVersion('CE') ) {
            return false;
        }

        $aEncodedFiles = array(
            "core/oxserial.php",
            "application/controllers/admin/shop_license.php",
            "core/oxconfk.php"
        );

        foreach ($aEncodedFiles as $sFileName) {
            $sFileName = getShopBasePath() . "/" . $sFileName;

            if (!file_exists($sFileName)) {
                throw new Exception("$sFileName does not exists");
            }

            $sContents = file_get_contents($sFileName);
            if (strstr($sContents, "class")) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check is all version files are removed correcly.
     *
     * @return boolean
     */
    protected function _versionFilesAreRemoved()
    {
        $aRemovedFiles = array();

        if ( $this->_isCurrentVersion('PE') || $this->_isCurrentVersion('CE') ) {
            $aRemovedFiles = array(
                "admin/admin_mall",
                "application/controllers/admin/adminlinks_mall.php",
                "application/controllers/admin/article_mall.php",
                "application/controllers/admin/attribute_mall.php",
                "application/controllers/admin/category_mall.php",
                "application/controllers/admin/delivery_mall.php",
                "application/controllers/admin/deliveryset_mall.php",
                "application/controllers/admin/discount_mall.php",
                "application/controllers/admin/news_mall.php",
                "application/controllers/admin/selectlist_mall.php",
                "application/controllers/admin/shop_mall.php",
                "application/controllers/admin/vendor_mall.php",
                "application/controllers/admin/voucherserie_mall.php",
                "application/controllers/admin/wrapping_mall.php",
                "application/views/admin/tpl/admin_mall.tpl",
                "application/views/admin/tpl/category_mall_nonparent.tpl",
                "application/views/admin/tpl/shop_mall.tpl",
                "application/controllers/mallstart.php",
                "core/oxadminrights.php"
            );
        }

        $sPath = getShopBasePath();

        foreach ($aRemovedFiles as $sFile) {
            if (file_exists($sPath . "/" . $sFile)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if all required methods are removed corecctly.
     *
     * @return mixed
     */
    protected function _areMethodsRemoved()
    {
        $aRemovedMethods = array();

        if ( $this->_isCurrentVersion('CE') ) {
            $aRemovedMethods = array(
                "oxstart::shopNotLicensed",
                "oxstart::shopLicensed",
                "shop_license::init",
                "shop_license::save",
                "shop_license::updateShopSerial",
                "shop_license::deleteSerial"
            );
        }

        $aRet = array();

        foreach ($aRemovedMethods as $sRMethod) {
            list($sClass, $sMethod) = explode("::", $sRMethod);
            $oInstance = new ReflectionClass($sClass);
            $blExists = false;
            try {
                if ($oInstance->getMethod($sMethod)->getDeclaringClass()->getName() == $oInstance->getName()) {
                    $blExists = true;
                }
            } catch (ReflectionException $e) {
            }
            if ($blExists) {
                $aRet[] = $sRMethod;
            }
        }

        if (!count($aRet)) {
            return false;
        }
        return implode("\n", $aRet);

    }

    /**
     * Check if correct file header is added.
     *
     * @return boolean
     */
    protected function _areHeadersCorrect()
    {
        //taking just one file
        $sHeader = $this->_areFilesEncoded()
                     ? "This Software is the property of OXID eSales and is protected"
                     : " * This Software is the property of OXID eSales and is protected";

        if ( $this->_isCurrentVersion('CE') ) {
            $sHeader = " *    OXID eShop Community Edition is free software: you can redistribute it and/or modify";
        }

        //taking just a single file
        $sContents = file_get_contents(getShopBasePath() . "/" . "core/oxconfig.php");

        $blHeadersOk = (bool)strstr($sContents, $sHeader);

        return $blHeadersOk;
    }

    /**
     * Detects if version is deployed, checks for at least one condition.
     *
     * @return boolean
     */
    protected function _isDeployed()
    {
        if (!is_null($this->_blIsDeployed)) {
            return $this->_blIsDeployed;
        }

        $this->_blIsDeployed = !$this->_svnDirExists() ||
                               $this->_nonSuffixesExists() ||
                               !$this->_versionTagsExists()  ||
                               $this->_versionFilesAreRemoved();

        if ( $this->_isCurrentVersion('EE') ) {
            $this->_blIsDeployed = $this->_blIsDeployed || $this->_areFilesEncoded();
        }

        return $this->_blIsDeployed;
    }

    /**
     * Test parser class.
     *
     * @return null
     */
    public function testParserClass()
    {
        if ($this->_isCurrentVersion('EE')) {
            $suffix = '_ee';
        }
        if ($this->_isCurrentVersion('PE')) {
            $suffix = '_pe';
        }
        if ($this->_isCurrentVersion('CE')) {
            $suffix = '_ce';
        }

        $sFile = realpath(dirname(realpath(__FILE__)).'/../../../../oxideshop_generic/continuous_integration/deployment/OxidDeployVersion.php');
        $sInFile = realpath(dirname(realpath(__FILE__)).'/deployerTestData/test.in.php');
        $sOutFile = realpath(dirname(realpath(__FILE__)).'/deployerTestData').'/test.out'.$suffix.'.php';

        if (!file_exists($sFile)) {
            if (trim(shell_exec('hostname')) != 'oxintegration' ) {
                $this->markTestSkipped('only oxintegration server is checking this');
            } else {
                $sFile = '/home/oxidlt/workspace/svn_continuous_integration/deployment/OxidDeployVersion.php';
                if (!file_exists($sFile)) {
                    $this->fail("file $sFile not found");
                }
            }
        }

        $sExpect = array (
            'EE' => 'standart tests
testing commented ones
EE
!PE
!CE
!PE_PE
testing normal ones
EE
!PE
!CE
!PE_PE
testing smarty ones
EE
!PE
!PE_PE
!PE_CE

single && tests
testing commented ones
testing normal ones
testing smarty ones

single || tests
testing commented ones
EE || PE
testing normal ones
EE || PE
testing smarty ones
EE || PE

testing tag in tag
4EE
5!CE
7!PE
8EE
10!PE
',

        'PE' => 'standart tests
testing commented ones
!EE
PE
!CE
PE_PE
testing normal ones
!EE
PE
!CE
PE_PE
testing smarty ones
!EE
PE
PE_PE
!PE_CE

single && tests
testing commented ones
!EE && PE
PE && PE_PE
testing normal ones
!EE && PE
PE && PE_PE
testing smarty ones
!EE && PE
PE && PE_PE

single || tests
testing commented ones
EE || PE
!EE || PE
PE || PE_PE
testing normal ones
EE || PE
!EE || PE
PE || PE_PE
testing smarty ones
EE || PE
!EE || PE
PE || PE_PE

testing tag in tag
1!EE
FAIL2PE
5!CE
6PE
',


        'CE' => 'standart tests
testing commented ones
!EE
PE
CE
!PE_PE
testing normal ones
!EE
PE
CE
!PE_PE
testing smarty ones
!EE
PE
!PE_PE
PE_CE

single && tests
testing commented ones
!EE && PE
testing normal ones
!EE && PE
testing smarty ones
!EE && PE

single || tests
testing commented ones
EE || PE
!EE || PE
PE || PE_PE
testing normal ones
EE || PE
!EE || PE
PE || PE_PE
testing smarty ones
EE || PE
!EE || PE
PE || PE_PE

testing tag in tag
1!EE
3CE
7!PE
9CE
10!PE
'
        );
        include_once $sFile;
        $o = new OxidDeployVersion('');
        foreach (array('EE', 'PE', 'CE') as $sVer) {
            $o->parseFile($sInFile, $sOutFile, $sVer);

            $sCode = "<?php  @date_default_timezone_set(@date_default_timezone_get());
                        include \"$sOutFile\";
                        ";
            $sOut = shell_exec("echo '$sCode' | php");
            $this->assertEquals(str_replace("\r", '', $sExpect[$sVer]), $sOut);
        }
        $o->clearTempFiles();
    }

    /**
     * Test if .svn directories are removed.
     *
     * @return null
     */
    public function testSvnDir()
    {
        if (!$this->_isDeployed()) {
            $this->markTestSkipped('NOT DEPLOYED VERSION');
        }
        $sDirs = $this->_svnDirExists();
        if ($sDirs) {
            $this->fail("----->>SVN dirs are not removed: $sDirs");
        }
    }

    /**
     * Test if version files are removed.
     *
     * @return null
     */
    public function testFilesRemoved()
    {
        if (!$this->_isDeployed()) {
            $this->markTestSkipped('NOT DEPLOYED VERSION');
        }

        if ($this->_versionFilesAreRemoved()) {
            return;
        }

        $this->fail("Some version files are not removed by the deployer");
    }

    /**
     * Test if version files are encoded.
     *
     * @return null
     */
    public function testIsEncoded()
    {
        if ( $this->_isCurrentVersion('PE') || $this->_isCurrentVersion('CE') ) {
            return;
        }

        if (!$this->_isDeployed()) {
            $this->markTestSkipped('NOT DEPLOYED VERSION');
        }

        if ($this->_areFilesEncoded())
            return;

        $this->fail("Some files are not properly encoded by the deployer");
    }

    /**
     * Test if version sufixes are removed.
     *
     * @return null
     */
    public function testSuffixes()
    {
        if (!$this->_isDeployed()) {
            $this->markTestSkipped('NOT DEPLOYED VERSION');
        }

        if ($sFail = $this->_nonSuffixesExists())
            $this->fail("----->>Version suffixes are not removed: $sFail");
    }

    /**
     * Test if version tags are removed.
     *
     * @return null
     */
    public function testVersionTags()
    {
        if (!$this->_isDeployed()) {
            $this->markTestSkipped('NOT DEPLOYED VERSION');
        }

        if ($sFail = $this->_versionTagsExists())
            $this->fail("----->>version tags exists ($sFail)");
    }

    /**
     * Test if file heades are replaced.
     *
     * @return null
     */
    public function testFileHeaders()
    {
        if (!$this->_isDeployed()) {
            $this->markTestSkipped('NOT DEPLOYED VERSION');
        }

        if (!$this->_areHeadersCorrect())
            $this->fail("Copyright headers are not ok");
    }

    /**
     * As this test case is intented to run only when version is deployed
     * by testUndeployedMethods() test we try do the detecting methods work at all.
     * And this could be tested localy from time to time
     *
     * @return null
     */
    public function testUndeployedMethods()
    {
        if (!$this->_isDeployed()) {
            $this->markTestSkipped('NOT DEPLOYED VERSION');
        }

        if ($sFail = $this->_areMethodsRemoved()) {
            $this->fail("Removed methods are not detected correctly: $sFail");
        }
    }

}
