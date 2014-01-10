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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version OXID eShop CE
 */


/*
class oxTestRegister {
    public static $callbacks = array();
    public static function execute() {
        foreach (self::$callbacks as $func) {
            call_user_func($func);
        }
    }
}
*/
require_once 'test_config.inc.php';

class OxidMockStubFunc implements PHPUnit_Framework_MockObject_Stub
{
    private $_func;

    public function __construct($sFunc)
    {
        $this->_func = $sFunc;
    }

    public function invoke(PHPUnit_Framework_MockObject_Invocation $invocation)
    {
        if (is_string($this->_func) && preg_match('/^\{.+\}$/', $this->_func)) {
            $args  = $invocation->parameters;
            $_this = $invocation->object;
            return eval($this->_func);
        } else {
            return call_user_func_array($this->_func, $invocation->parameters);
        }
    }

    public function toString()
    {
        return 'call user-specified function '.$this->_func;
    }
}

class OxidTestCase extends PHPUnit_Framework_TestCase
{
    protected $_aBackup = array();
    protected static $_aInitialDbChecksum = null;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        $this->_aBackup['_SERVER']  = $_SERVER;
        $this->_aBackup['_POST']    = $_POST;
        $this->_aBackup['_GET']     = $_GET;
        $this->_aBackup['_SESSION'] = $_SESSION;
        $this->_aBackup['_COOKIE']  = $_COOKIE;

        parent::setUp();
        error_reporting( (E_ALL ^ E_NOTICE) | E_STRICT );
        ini_set('display_errors', true);

        modConfig::getInstance();
        modSession::getInstance();

        oxUtils::getInstance()->cleanStaticCache();
        error_reporting( (E_ALL ^ E_NOTICE) | E_STRICT );
        ini_set('display_errors', true);
    }

    /**
     * This method is called after the last test of this test class is run.
     *
     * @since Method available since Release 3.4.0
     */
    public static function tearDownAfterClass()
    {
        modSession::getInstance()->cleanup();
        modDb::getInstance()->cleanup();
        modConfig::getInstance()->cleanup();

        self::checkDbChecksums();
        if (function_exists('memory_get_usage')) {
            echo "\n".round(memory_get_usage(1) / 1024 / 1024) .'M ('.round(memory_get_peak_usage(1) / 1024 / 1024) .'M)'."\n";
        }
        //print_r(oxSuperCfg::$DBG);
        //print_r(oxSuperCfg::$DBG_TRACE);
    }

    /**
     * Executed after test is down
     *
     */
    protected function tearDown()
    {
        overrideGetShopBasePath(null);

        oxTestsStaticCleaner::clean('oxSeoEncoder', '_instance');
        oxTestsStaticCleaner::clean('oxSeoEncoderArticle', '_instance');
        oxTestsStaticCleaner::clean('oxSeoEncoderCategory', '_instance');
        oxTestsStaticCleaner::clean('oxVatSelector', '_instance');
        oxTestsStaticCleaner::clean('oxDiscountList', '_instance');
        oxTestsStaticCleaner::clean('oxUtilsObject', '_aInstanceCache');
        oxTestsStaticCleaner::clean('oxArticle', '_aLoadedParents');

        modInstances::cleanup();
        oxTestModules::cleanUp();
        modOxid::globalCleanup();

        modSession::getInstance()->cleanup();
        modDb::getInstance()->cleanup();
        modConfig::getInstance()->cleanup();

        $_SERVER  = $this->_aBackup['_SERVER'];
        $_POST    = $this->_aBackup['_POST'];
        $_GET     = $this->_aBackup['_GET'];
        $_SESSION = $this->_aBackup['_SESSION'];
        $_COOKIE  = $this->_aBackup['_COOKIE'];

        parent::tearDown();
        //oxTestRegister::execute();
    }

    /**
     * checks if cleanup is a success
     */
    protected function sharedAssertions()
    {
        // it runs before tearDown, so our tearDown will be able to use db again
        modDb::getInstance()->cleanup();
    }

    /**
     * eval Func for invoke mock
     *
     * @param mixed $value
     * @access protected
     * @return void
     */
    protected function evalFunction($value)
    {
        return new OxidMockStubFunc($value);
    }

    /**
     * initialize self::$_aInitialDbChecksum
     *
     * @todo move to separate class instead of static
     *
     * @return null
     */
    public static function initializeDbChecksum()
    {
        self::$_aInitialDbChecksum = self::getDbChecksum();
    }

    /**
     * get self::$_aInitialDbChecksum
     *
     * @todo move to separate class instead of static
     *
     * @return array
     */
    protected static function _getInitialDbChecksum()
    {
        if (!self::$_aInitialDbChecksum) {
            throw new Exception("DB Checksums not initialized");
        }
        return self::$_aInitialDbChecksum;
    }

    /**
     * check current DB checksums (vs initial)
     *
     * @todo move to separate class instead of static
     *
     * @param PHPUnit_Framework_TestCase $oTest
     * @param PHPUnit_Framework_TestResult $result
     */
    public static function checkDbChecksums(PHPUnit_Framework_TestCase $oTest = null, PHPUnit_Framework_TestResult $result = null)
    {
        $aDiff = array_diff(self::getDbChecksum(), self::_getInitialDbChecksum());
        if ( count($aDiff) > 0 ) {
            if ($oTest && $result) {
                $result->addError( $oTest,
                               new RuntimeException("Changed tables: ".implode(" ", array_keys($aDiff))),
                               0
                            );
            }
            $dbM = new dbMaintenance();
            $dbM->restoreDB ( MAINTENANCE_WHOLETABLES, MAINTENANCE_MODE_ONLYRESET );
            echo ("|");
        }
    }

    public function run(PHPUnit_Framework_TestResult $result = null)
    {
        if (!self::$_aInitialDbChecksum) {
            self::initializeDbChecksum();
        }

        $result = parent::run($result);

        oxTestModules::cleanUp();
        return $result;
    }

    /**
     * Cleans up table
     *
     * @param string $sTable
     */
    public function cleanUpTable($sTable, $sColName = null)
    {
        $sCol = ( !empty($sColName) ) ? $sColName : 'oxid';

        //deletes allrecords where oxid or specified column name values starts with underscore(_)
        $sQ = "delete from $sTable where $sCol like '\_%' ";
        oxDb::getDb()->Execute($sQ);
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
     * @param array|null $constructorParams parameters for contructor
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
     *
     */
    public function cleanTmpDir()
    {
        $sDirName = oxConfig::getInstance()->getConfigParam('sCompileDir');
        if (DIRECTORY_SEPARATOR == '\\') {
            $sDirName = str_replace( '/', "\\", $sDirName);
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
     * Converts a string to UTF format.
     *
     * @todo move to separate class instead of static
     *
     * @return array
     */
    protected static function getDbChecksum()
    {
        $myDB = oxDb::getDB();
        $myConfig = modConfig::getInstance();
        $sSelect = 'select t.TABLE_NAME from INFORMATION_SCHEMA.tables as t where t.TABLE_SCHEMA = "'.$myConfig->getConfigParam( 'dbName' ).'" and t.TABLE_NAME not like "oxv_%"';
        $aTables = $myDB->getCol($sSelect);
        $sTableSet = implode(", ", $aTables);
        $sSelect = 'CHECKSUM TABLE '.$sTableSet;
        $rs = $myDB->execute($sSelect);
        if ( $rs != false && $rs->RecordCount() > 0 ) {
            while ( !$rs->EOF ) {
                if ( stripos($rs->fields[0], "oxv_") !== 0 ) {
                    $aChecksum[$rs->fields[0]] = $rs->fields[1];
                }
                $rs->MoveNext();
            }
        }
        return $aChecksum;
    }
}
