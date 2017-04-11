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

require_once TEST_LIBRARY_PATH . 'test_config.inc.php';

if (defined('SHOPRESTORATIONCLASS') && file_exists(TEST_LIBRARY_PATH . SHOPRESTORATIONCLASS.".php")) {
    include_once TEST_LIBRARY_PATH . SHOPRESTORATIONCLASS.".php";
} else {
    include_once TEST_LIBRARY_PATH . "dbRestore.php";
}

/**
 * Class for creating stub objects.
 */
class OxidMockStubFunc implements PHPUnit_Framework_MockObject_Stub
{

    private $_func;

    /**
     * Constructor
     *
     * @param string $sFunc
     */
    public function __construct($sFunc)
    {
        $this->_func = $sFunc;
    }

    /**
     * Fakes the processing of the invocation $invocation by returning a
     * specific value.
     *
     * @param PHPUnit_Framework_MockObject_Invocation $invocation
     * The invocation which was mocked and matched by the current method
     * and argument matchers.
     *
     * @return mixed
     */
    public function invoke(PHPUnit_Framework_MockObject_Invocation $invocation)
    {
        if (is_string($this->_func) && preg_match('/^\{.+\}$/', $this->_func)) {
            $args = $invocation->parameters;
            $_this = $invocation->object;

            return eval($this->_func);
        } else {
            return call_user_func_array($this->_func, $invocation->parameters);
        }
    }

    /**
     * Returns user called function.
     *
     * @return string
     */
    public function toString()
    {
        return 'call user-specified function ' . $this->_func;
    }
}

/**
 * Base tests class. Most tests should extend this class.
 */
class OxidTestCase extends PHPUnit_Framework_TestCase
{
    /** @var array Request parameters backup. */
    protected $_aBackup = array();

    /** @var array Registry cache. */
    private static $_aRegistryCache = null;

    /** @var bool Registry cache. */
    private static $_blSetupBeforeTestSuiteDone = false;

    /** @var DbRestore Database restorer object */
    protected static $_oDbRestore = null;

    /** @var oxTestModuleLoader Module loader. */
    protected static $_oModuleLoader = null;

    /** @var array multishop tables used in shop */
    protected $_aMultiShopTables = array(
        'oxarticles', 'oxcategories', 'oxattribute', 'oxdelivery',
        'oxdeliveryset', 'oxdiscount', 'oxmanufacturers', 'oxselectlist',
        'oxvendor', 'oxvoucherseries', 'oxwrapping'
    );

    /** @var array variable */
    protected $_aTeardownSqls = array();

    /** @var array tables for cleaning */
    protected $_aTableForCleanups = array();

    /**
     * Running setUpBeforeTestSuite action.
     *
     * @param string $name
     * @param array  $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        if (!self::$_blSetupBeforeTestSuiteDone) {
            $this->setUpBeforeTestSuite();
            self::$_blSetupBeforeTestSuiteDone = true;
        }
        parent::__construct($name, $data, $dataName);
    }

    /**
     * Runs necessary things before running tests suite.
     */
    public function setUpBeforeTestSuite()
    {
        if (MODULES_PATH) {
            $oTestModuleLoader = $this->_getModuleLoader();
            $oTestModuleLoader->loadModules(explode(',', MODULES_PATH));
            $oTestModuleLoader->setModuleInformation();
        }

        $this->_backupDatabase();

        oxRegistry::getUtils()->commitFileCache();

        $oxLang = oxRegistry::getLang();
        $oxLang->resetBaseLanguage();

        $this->_backupRegistry();
        $this->_backupRequestVariables();
    }

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        if (getenv('TRAVIS_ERROR_LEVEL')) {
            error_reporting((int)getenv('TRAVIS_ERROR_LEVEL'));
        } else {
            error_reporting((E_ALL ^ E_NOTICE) | E_STRICT);
        }

        ini_set('display_errors', true);

        $this->getConfig();
        $this->getSession();
        $this->setAdminMode(false);
        $this->setShopId(null);
        oxAddClassModule('modOxUtilsDate', 'oxUtilsDate');

        oxRegistry::getUtils()->cleanStaticCache();

        if (MODULES_PATH) {
            $oTestModuleLoader = $this->_getModuleLoader();
            $oTestModuleLoader->setModuleInformation();
        }

        if (getenv('TRAVIS_ERROR_LEVEL')) {
            error_reporting((int)getenv('TRAVIS_ERROR_LEVEL'));
        } else {
            error_reporting((E_ALL ^ E_NOTICE) | E_STRICT);
        }

        ini_set('display_errors', true);
    }

    /**
     * Starts test.
     *
     * @param PHPUnit_Framework_TestResult $result
     *
     * @return PHPUnit_Framework_TestResult
     */
    public function run(PHPUnit_Framework_TestResult $result = null)
    {
        $this->_removeBlacklistedClassesFromCodeCoverage($result);
        $result = parent::run($result);

        oxTestModules::cleanUp();
        return $result;
    }

    /**
     * Executed after test is down.
     * Cleans up database only if test does not have dependencies.
     * If test does have dependencies, any value instead of null should be returned.
     */
    protected function tearDown()
    {
        if ($this->getResult() === null) {
            $this->cleanUpDatabase();

            modDb::getInstance()->modAttach(modDb::getInstance()->getRealInstance());
            oxTestsStaticCleaner::clean('oxUtilsObject', '_aInstanceCache');
            oxTestsStaticCleaner::clean('oxArticle', '_aLoadedParents');

            modInstances::cleanup();
            oxTestModules::cleanUp();
            modOxid::globalCleanup();
            modDB::getInstance()->cleanup();

            $this->getSession()->cleanup();
            $this->getConfig()->cleanup();

            $this->_resetRequestVariables();
            $this->_resetRegistry();

            oxUtilsObject::resetClassInstances();
            oxUtilsObject::resetModuleVars();

            parent::tearDown();
        }
    }

    /**
     * This method is called after the last test of this test class is run.
     */
    public static function tearDownAfterClass()
    {
        $oDbRestore = self::_getDbRestore();
        $oDbRestore->restoreDB();

        if (function_exists('memory_get_usage') && !getenv('TRAVIS_ERROR_LEVEL')) {
            echo "\n" . round(memory_get_usage(1) / 1024 / 1024) . 'M (' . round(memory_get_peak_usage(1) / 1024 / 1024) . 'M)' . "\n";
        }
    }

    /**
     * Get parameter from session object.
     *
     * @param string $sParam parameter name.
     *
     * @return mixed
     */
    public function getSessionParam($sParam)
    {
        return $this->getSession()->getVar($sParam);
    }

    /**
     * Set parameter to session object.
     *
     * @param string $sParam Parameter name.
     * @param object $oVal   Any parameter value, default null.
     */
    public function setSessionParam($sParam, $oVal = null)
    {
        $this->getSession()->setVar($sParam, $oVal);
    }

    /**
     * Get parameter from config request object.
     *
     * @param string $sParam parameter name.
     *
     * @return mixed
     */
    public function getRequestParam($sParam)
    {
        return $this->getConfig()->getRequestParameter($sParam);
    }

    /**
     * Set parameter to config request object.
     *
     * @param string $sParam Parameter name.
     * @param mixed  $mxVal  Any parameter value, default null.
     */
    public function setRequestParam($sParam, $mxVal = null)
    {
        $this->getConfig()->setRequestParameter($sParam, $mxVal);
    }

    /**
     * Get parameter from config object.
     *
     * @param string $sParam parameter name.
     *
     * @return mixed
     */
    public function getConfigParam($sParam)
    {
        return $this->getConfig()->getConfigParam($sParam);
    }

    /**
     * Set parameter to config object.
     *
     * @param string $sParam Parameter name.
     * @param mixed  $mxVal  Any parameter value, default null.
     */
    public function setConfigParam($sParam, $mxVal = null)
    {
        $this->getConfig()->setConfigParam($sParam, $mxVal);
    }

    /**
     * Sets OXID shop admin mode.
     *
     * @param bool $blAdmin set to admin mode TRUE / FALSE.
     */
    public function setAdminMode($blAdmin)
    {
        $this->setSessionParam('blIsAdmin', $blAdmin);
        $this->getConfig()->setAdminMode($blAdmin);
    }

    /**
     * Get OXID shop ID.
     *
     * @return string
     */
    public function getShopId()
    {
        return $this->getConfig()->getShopId();
    }

    /**
     * Sets OXID shop ID.
     *
     * @param string $sShopId set active shop ID.
     */
    public function setShopId($sShopId)
    {
        $this->getConfig()->setShopId($sShopId);
    }

    /**
     * Set static time value for testing.
     *
     * @param int $oVal
     */
    public function setTime($oVal = null)
    {
        modOxUtilsDate::getInstance()->UNITSetTime($oVal);
    }

    /**
     * Get static / real time value for testing.
     *
     * @return int
     */
    public function getTime()
    {
        return modOxUtilsDate::getInstance()->getTime();
    }

    /**
     * Returns session object
     *
     * @return oxSession
     */
    public static function getSession()
    {
        return modSession::getInstance();
    }

    /**
     * Returns config object
     *
     * @return oxConfig
     */
    public static function getConfig()
    {
        return modConfig::getInstance();
    }

    /**
     * Returns database object
     *
     * @param int $iFetchMode
     *
     * @return oxLegacyDb
     */
    public static function getDb($iFetchMode = null)
    {
        $oDB = oxDb::getDb();
        if ($iFetchMode !== null) {
            $oDB->setFetchMode($iFetchMode);
        }

        return $oDB;
    }

    /**
     * Returns cache backend
     *
     * @return oxCacheBackend
     */
    public function getCacheBackend()
    {
        return oxRegistry::get('oxCacheBackend');
    }

    /**
     * Sets language
     *
     * @param int $iLangId
     */
    public function setLanguage($iLangId)
    {
        $oxLang = oxRegistry::getLang();
        $oxLang->setBaseLanguage($iLangId);
        $oxLang->setTplLanguage($iLangId);
    }

    /**
     * Returns currently set language
     *
     * @return string
     */
    public function getLanguage()
    {
        return oxRegistry::getLang()->getBaseLanguage();
    }

    /**
     * Sets template language
     *
     * @param int $iLangId
     */
    public function setTplLanguage($iLangId)
    {
        oxRegistry::getLang()->setTplLanguage($iLangId);
    }

    /**
     * Returns template language
     *
     * @return string
     */
    public function getTplLanguage()
    {
        return oxRegistry::getLang()->getTplLanguage();
    }

    /**
     * Set sqls to be executed on tearDown
     *
     * @param array $aRevertSqls
     */
    public function setTeardownSqls($aRevertSqls)
    {
        $this->_aTeardownSqls = $aRevertSqls;
    }

    /**
     * Get teardown sqls containing delete information
     *
     * @return array
     */
    public function getTeardownSqls()
    {
        return (array)$this->_aTeardownSqls;
    }

    /**
     * Add single teardown sql
     *
     * @param string $sSql teardown sql
     */
    public function addTeardownSql($sSql)
    {
        if (!in_array($sSql, $this->_aTeardownSqls)) {
            $this->_aTeardownSqls[] = $sSql;
        }
    }

    /**
     * Set multishop tables array, in case some custom tables need to be used
     *
     * @param array $aMultiShopTables
     */
    public function setMultiShopTables($aMultiShopTables)
    {
        $this->_aMultiShopTables = $aMultiShopTables;
    }

    /**
     * Get multishop tables array
     *
     * @return array
     */
    public function getMultiShopTables()
    {
        return $this->_aMultiShopTables;
    }

    /**
     * Mark the test as skipped until given date.
     * Wrapper function for PHPUnit_Framework_Assert::markTestSkipped.
     *
     * @param string $sDate    Date string in format 'Y-m-d'.
     * @param string $sMessage Message.
     *
     * @throws PHPUnit_Framework_SkippedTestError
     */
    public function markTestSkippedUntil($sDate, $sMessage = '')
    {
        $oDate = DateTime::createFromFormat('Y-m-d', $sDate);

        if (time() < ((int)$oDate->format('U'))) {
            $this->markTestSkipped($sMessage);
        }
    }

    /**
     * Executes SQL and adds table to clean up after test.
     * For EE version elements are added to map table for specified shops.
     *
     * @param string    $sSql     Sql to be executed.
     * @param string    $sTable   Table name.
     * @param array|int $aShopIds List of shop IDs.
     * @param null      $sMapId   Map ID.
     */
    public function addToDatabase($sSql, $sTable, $aShopIds = 1, $sMapId = null)
    {
        oxDb::getDb()->execute($sSql);

    }

    /**
     * Calls all the queries stored in $_aTeardownSqls
     * Cleans all the tables that were set
     */
    public function cleanUpDatabase()
    {
        if ($tearDownQueries = $this->getTeardownSqls()) {
            foreach ($tearDownQueries as $query) {
                oxDb::getDb()->execute($query);
            }
        }

        if ($aTablesForCleanup = $this->getTablesForCleanup()) {
            $oDbRestore = $this->_getDbRestore();
            foreach ($aTablesForCleanup as $sTable) {
                $oDbRestore->restoreTable($sTable);
            }
        }
    }

    /**
     * Gets dirty tables for cleaning
     *
     * @param array $aTablesForCleanup
     */
    public function setTablesForCleanup($aTablesForCleanup)
    {
        $this->_aTableForCleanups = $aTablesForCleanup;
    }

    /**
     * Sets dirty tables for cleaning
     *
     * @return array
     */
    public function getTablesForCleanup()
    {
        return (array)$this->_aTableForCleanups;
    }

    /**
     * Adds table to be cleaned on teardown
     *
     * @param string $sTable
     */
    public function addTableForCleanup($sTable)
    {
        if (!in_array($sTable, $this->_aTableForCleanups)) {
            $this->_aTableForCleanups[] = $sTable;
        }
    }

    /**
     * Cleans up table
     *
     * @param string $sTable   Table name
     * @param string $sColName Column name
     */
    public function cleanUpTable($sTable, $sColName = null)
    {
        $sCol = (!empty($sColName)) ? $sColName : 'oxid';


        //deletes allrecords where oxid or specified column name values starts with underscore(_)
        $sQ = "delete from `$sTable` where `$sCol` like '\_%' ";
        $this->getDb()->execute($sQ);
    }

    /**
     * Create proxy class of given class. Proxy allows to test of protected class methods and to access non public members
     *
     * @param string $superClassName
     *
     * @return string
     */
    public function getProxyClassName($superClassName)
    {
        $proxyClassName = "{$superClassName}Proxy";

        if (!class_exists($proxyClassName, false)) {

            $class = "
                class $proxyClassName extends $superClassName
                {
                    public function __call(\$function, \$args)
                    {
                        \$function = str_replace('UNIT', '_', \$function);
                        if(method_exists(\$this,\$function)){
                            return call_user_func_array(array(&\$this, \$function),  \$args);
                        }else{
                            throw new Exception('Method '.\$function.' in class '.get_class(\$this).' does not exist');
                        }
                    }
                    public function setNonPublicVar(\$name, \$value)
                    {
                        \$this->\$name = \$value;
                    }

                    public function getNonPublicVar(\$name)
                    {
                        return \$this->\$name;
                    }
                }";
            eval($class);
        }

        return $proxyClassName;
    }

    /**
     * Create proxy of given class. Proxy allows to test of protected class methods and to access non public members
     *
     * @param string $superClassName
     * @param array  $params
     *
     * @deprecated
     *
     * @return object
     */
    public function getProxyClass($superClassName, array $params = null)
    {
        $proxyClassName = $this->getProxyClassName($superClassName);
        if (!empty($params)) {
            // Create an instance using Reflection, because constructor has parameters
            $class = new ReflectionClass($proxyClassName);
            $instance = $class->newInstanceArgs($params);
        } else {
            $instance = new $proxyClassName();
        }

        return $instance;
    }

    /**
     * Cleans tmp dir.
     */
    public function cleanTmpDir()
    {
        $sDirName = oxRegistry::getConfig()->getConfigParam('sCompileDir');
        if (DIRECTORY_SEPARATOR == '\\') {
            $sDirName = str_replace('/', "\\", $sDirName);
            system("del $sDirName\\*.txt");
            system("del $sDirName\\ox*.tmp");
            system("del $sDirName\\*.tpl.php");
        } else {
            system("rm -f $sDirName/*.txt");
            system("rm -f $sDirName/ox*.tmp");
            system("rm -f $sDirName/*.tpl.php");
        }
    }

    /**
     * Change to virtual file with vfstream when available.
     *
     * @param string $sFileName
     * @param string $sFileContent
     *
     * @usage Create file from file name and file content to oxCCTempDir.
     *
     * @return string path to file
     */
    public function createFile($sFileName, $sFileContent)
    {
        $sPathToFile = oxCCTempDir . '/' . $sFileName;

        $sDirectory = dirname($sPathToFile);
        if (!file_exists($sDirectory)) {
            mkdir($sDirectory, 0777, true);
        }

        file_put_contents($sPathToFile, $sFileContent);

        return $sPathToFile;
    }

    /**
     * Returns database restorer object.
     *
     * @return DbRestore
     */
    protected static function _getDbRestore()
    {
        if (is_null(self::$_oDbRestore)) {
            self::$_oDbRestore = new DbRestore();
        }

        return self::$_oDbRestore;
    }

    /**
     * Returns database restorer object.
     *
     * @return oxTestModuleLoader
     */
    protected static function _getModuleLoader()
    {
        if (is_null(self::$_oModuleLoader)) {
            self::$_oModuleLoader = new oxTestModuleLoader();
        }

        return self::$_oModuleLoader;
    }

    /**
     * Creates registry clone
     */
    protected function _backupRegistry()
    {
        self::$_aRegistryCache = array();
        foreach (oxRegistry::getKeys() as $class) {
            $instance = oxRegistry::get($class);
            self::$_aRegistryCache[$class] = clone $instance;
        }
    }

    /**
     * Cleans up the registry
     */
    protected function _resetRegistry()
    {
        $aRegKeys = oxRegistry::getKeys();

        $aSkippedClasses = array("oxconfigfile");

        foreach ($aRegKeys as $sKey) {
            if (!in_array($sKey, $aSkippedClasses)) {
                $oInstance = null;
                if (!isset(self::$_aRegistryCache[$sKey])) {
                    try {
                        $oNewInstance = oxNew($sKey);
                        self::$_aRegistryCache[$sKey] = $oNewInstance;
                    } catch (oxSystemComponentException $oException) {
                        oxRegistry::set($sKey, null);
                        continue;
                    }
                }
                $oInstance = clone self::$_aRegistryCache[$sKey];
                oxRegistry::set($sKey, $oInstance);
            }
        }
    }

    /**
     * eval Func for invoke mock
     *
     * @param mixed $value
     *
     * @access protected
     *
     * @return OxidMockStubFunc
     */
    protected function evalFunction($value)
    {
        return new OxidMockStubFunc($value);
    }

    /**
     * Converts a string to UTF format.
     *
     * @param string $sVal
     *
     * @return string
     */
    protected function _2Utf($sVal)
    {
        return iconv("ISO-8859-1", "UTF-8", $sVal);
    }

    /**
     * Backs up database for later restorations.
     */
    protected function _backupDatabase()
    {
        $oDbRestore = self::_getDbRestore();
        $oDbRestore->dumpDB();
    }

    /**
     * Creates stub object from given class
     *
     * @param string $sClass       Class name
     * @param array  $aMethods     Assoc array with method => value
     * @param array  $aTestMethods Array with test methods for mocking
     *
     * @return mixed
     */
    protected function _createStub($sClass, $aMethods, $aTestMethods = array())
    {
        $aMockedMethods = array_unique(array_merge(array_keys($aMethods), $aTestMethods));

        $oObject = $this->getMock($sClass, $aMockedMethods, array(), '', false);

        foreach ($aMethods as $sMethod => $sValue) {
            if (!in_array($sMethod, $aTestMethods)) {
                $oObject->expects($this->any())
                    ->method($sMethod)
                    ->will($this->returnValue($sValue));
            }
        }

        return $oObject;
    }

    /**
     * Backs up global request variables for reverting them back after test run.
     */
    protected function _backupRequestVariables()
    {
        $this->_aBackup['_SERVER'] = $_SERVER;
        $this->_aBackup['_POST'] = $_POST;
        $this->_aBackup['_GET'] = $_GET;
        $this->_aBackup['_SESSION'] = $_SESSION;
        $this->_aBackup['_COOKIE'] = $_COOKIE;
    }

    /**
     * Sets global request variables to backed up ones after every test run.
     */
    protected function _resetRequestVariables()
    {
        $_SERVER = $this->_aBackup['_SERVER'];
        $_POST = $this->_aBackup['_POST'];
        $_GET = $this->_aBackup['_GET'];
        $_SESSION = $this->_aBackup['_SESSION'];
        $_COOKIE = $this->_aBackup['_COOKIE'];
    }

    /**
     * Removes blacklisted classes from code coverage report, as this is only fixed in PHPUnit 4.0.
     *
     * @param PHPUnit_Framework_TestResult $result
     */
    private function _removeBlacklistedClassesFromCodeCoverage($result)
    {
        if ($result->getCollectCodeCoverageInformation()) {
            $oCoverage = $result->getCodeCoverage();
            $oFilter = $oCoverage->filter();
            $aBlacklist = $oFilter->getBlacklist();
            foreach ($aBlacklist as $sFile) {
                $oFilter->removeFileFromWhitelist($sFile);
            }
        }
    }
}
