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

define ('MAX_LOOP_AMOUNT', 4);
function getRandLTAmnt()
{
    return rand(1, MAX_LOOP_AMOUNT - 1);
}

//returns the base path, no the unit path
function getTestBasePath()
{
    $sPath = dirname(realpath(__FILE__));
    $sPath = substr($sPath, 0, strrpos($sPath, "\\"));

    return $sPath;
}

/**
 * adds new module to specified class
 * Usable if you want to check how many calls of class AA method BB
 *    done while testing class XX.
 * Or can be used to disable some AA method like BB (e.g. die),
 *    which gets called while testing XX.
 * Since there are no modules in testing data, this function does not
 *    check module parent module
 *
 * e.g.
 *  - we need to disable oxUtils::showMessageAndDie
 *     class modUtils extends oxutils {
 *        function showMessageAndDie (){}
 *     };
 *  - and then in your test function
 *     oxAddClassModule('modUtils', 'oxutils');
 *  - and after doing some ...
 *     oxRemClassModule('modUtils');
 *
 *  - now one can provide his/her own oxClassCacheKey
 *    found it usefull while testing getInstance method, when I needed to know
 *    that my object is really really created, and not taken from cache.
 */
function oxClassCacheKey($reset = false, $sProvide = null)
{
    static $key = null;
    if ($key === null || $reset) {
        if ($sProvide) {
            $key = $sProvide;
        } else {
            $myConfig = modConfig::getInstance();
            if (is_array($myConfig->getConfigParam('aModules'))) {
                $key = md5('cc|' . implode('|', $myConfig->getConfigParam('aModules')));
            } else {
                $key = md5('cc|');
            }
        }
    }

    return $key;
}

function oxAddClassModule($sModuleClass, $sClass)
{
    //$myConfig = modConfig::getInstance();
    //$aModules = $myConfig->getConfigParam( 'aModules' );
    $oFactory = new oxUtilsObject();
    $aModules = $oFactory->getModuleVar("aModules");

    //unsetting _possible_ registry instance
    oxRegistry::set($sClass, null);

    if ($aModules[strtolower($sClass)]) {
        $sModuleClass = $aModules[strtolower($sClass)] . '&' . $sModuleClass;
    }
    $aModules[strtolower($sClass)] = $sModuleClass;

    //$myConfig->setConfigParam( 'aModules', $aModules );
    $oFactory->setModuleVar("aModules", $aModules);

    oxClassCacheKey(true);
}

function oxRemClassModule($sModuleClass, $sClass = '')
{
    //$myConfig = modConfig::getInstance();
    //$aModules = $myConfig->getConfigParam( 'aModules' );

    //unsetting _possible_ registry instance
    oxRegistry::set($sClass, null);

    $oFactory = new oxUtilsObject();
    $aModules = $oFactory->getModuleVar("aModules");

    if (!$aModules) {
        $aModules = array();
    }

    if ($sClass) {
// force for now        if ($aModules[$sClass] == $sModuleClass)
        unset($aModules[$sClass]);
    } else {
        while (($sKey = array_search($sModuleClass, $aModules)) !== false) {
            unset($aModules[$sKey]);
        }
    }
    //$myConfig->setConfigParam( 'aModules', $aModules );
    $oFactory->setModuleVar("aModules", $aModules);

    oxClassCacheKey(true);
}

/**
 * Class oxTestModules
 *
 * @deprecated
 */
class oxTestModules
{

    private static $_addedmods = array();

    private static function _getNextName($sOrig)
    {
        $base = $sOrig . '__oxTestModule_';
        $cnt = 0;
        while (class_exists($base . $cnt, false)) {
            ++$cnt;
        }

        return $base . $cnt;
    }

    /**
     * addVar adds module and creates function in it
     *
     * @param string $class   target class
     * @param string $varName target variabe
     * @param string $access  public | private | public static, whatever
     * @param string $default default value
     *
     * @static
     * @access public
     * @return void
     */
    public static function addVariable($class, $varName, $access = 'public', $default = 'null')
    {
        $class = strtolower($class);
        $name = self::_getNextName($class);
        if ($cnt = count(self::$_addedmods[$class])) {
            $last = self::$_addedmods[$class][$cnt - 1];
        } else {
            $last = $class;
        }
        eval ("class $name extends $last { $access \$$varName = $default;}");
        oxAddClassModule($name, $class);
        self::$_addedmods[$class][] = $name;
    }

    /**
     * addFunction adds module and creates function in it
     *
     * @param mixed $class   target class
     * @param mixed $fncName target function
     * @param mixed $func    function - if it is '{...}' then it is function code ($aA is arguments array), else it is taken as param to call_user_func_array
     *
     * @static
     * @access public
     * @return string
     */
    public static function addFunction($class, $fncName, $func)
    {
        $class = strtolower($class);
        $name = self::_getNextName($class);

        if ($cnt = count(self::$_addedmods[$class])) {
            $last = self::$_addedmods[$class][$cnt - 1];
        } else {
            $last = $class;
        }
        $sCode = '';
        if (preg_match('/^{.*}$/ms', $func)) {
            $sCode = "\$aA = func_get_args(); " . trim($func, '{}');
        } else {
            if (preg_match('/^[a-z0-9_-]*$/i', trim($func))) {
                $func = "'$func'";
            }
            $sCode = " \$arg = func_get_args(); return call_user_func_array($func, \$arg);";
        }

        if (!getenv('TRAVIS_ERROR_LEVEL')) {
            $iErrorReportinc = error_reporting(E_ALL ^ E_NOTICE);
        }

        $aFncParams = array();
        if (strpos($fncName, '(') !== false) {
            $aMatches = null;
            preg_match("@(.*?)\((.*?)\)@", $fncName, $aMatches);

            $fncName = trim($aMatches[1]);
            if (trim($aMatches[2])) {
                $aFncParams = explode(',', $aMatches[2]);
            } else {
                $aFncParams = array();
            }
        }

        if (method_exists($last, $fncName)) {
            $oReflection = new ReflectionClass($last);
            $aMethodParams = $oReflection->getMethod($fncName)->getParameters();

            $fncName .= '(';
            $blFirst = true;
            foreach ($aMethodParams AS $iKey => $oParam) {

                if (!$blFirst) {
                    $fncName .= ', ';
                } else {
                    $blFirst = false;
                }

                if (isset($aFncParams[$iKey])) {
                    $fncName .= $aFncParams[$iKey];

                    if (strpos($aFncParams[$iKey], '=') === false && $oParam->isDefaultValueAvailable()) {
                        $fncName .= ' = ' . var_export($oParam->getDefaultValue(), true);
                    }

                    continue;
                }

                if ($oParam->getClass()) {
                    $fncName .= $oParam->getClass()->getName() . ' ';
                }
                $fncName .= '$' . $oParam->getName();
                if ($oParam->isDefaultValueAvailable()) {
                    $fncName .= ' = ' . var_export($oParam->getDefaultValue(), true);
                }
            }
            $fncName .= ')';
        } else {
            if (empty($aFncParams)) {
                $fncName .= '($p1=null, $p2=null, $p3=null, $p4=null, $p5=null, $p6=null, $p7=null, $p8=null, $p9=null, $p10=null)';
            } else {
                $fncName .= '(' . implode(', ', $aFncParams) . ')';
            }
        }

        eval ("class $name extends $last { function $fncName { $sCode }}");
        oxAddClassModule($name, $class);

        if (!getenv('TRAVIS_ERROR_LEVEL')) {
            error_reporting($iErrorReportinc);
        }

        self::$_addedmods[$class][] = $name;

        return $name;
    }

    /**
     * internal class->object map
     *
     * @var array
     */
    protected static $_aModuleMap = array();
    protected static $_oOrigOxUtilsObj = null;

    /**
     * add object to be returned from oxNew for a class
     *
     * @param string $sClassName
     * @param object $oObject
     *
     * @return null
     */
    public static function addModuleObject($sClassName, $oObject)
    {
        oxRegistry::set($sClassName, null);
        oxUtilsObject::setClassInstance($sClassName, $oObject);
        /*
        $sClassName = strtolower($sClassName);
        if (!self::$_oOrigOxUtilsObj) {
            self::$_oOrigOxUtilsObj = oxUtilsObject::getInstance();
            self::addFunction('oxUtilsObject', 'oxNew($class)', '{return oxTestModules::getModuleObject($class);}');
        }
        self::$_aModuleMap[$sClassName] = $oObject;
        */
    }

    /**
     * rewrittern oxNew logic to return object from the map
     *
     * @param string $sClassName
     *
     * @return object
     */
    /*public static function getModuleObject($sClassName)
    {
        $sClassName = strtolower($sClassName);
        if (isset(self::$_aModuleMap[$sClassName])) {
            return self::$_aModuleMap[$sClassName];
        }
        if (!self::$_oOrigOxUtilsObj) {
            throw new Exception("TEST ERROR: original oxUtilsObject is badly initialized");
        }
        return self::$_oOrigOxUtilsObj->oxNew($sClassName);
    }*/

    /**
     * publicize method = creates a wrapper for it named p_XXX instead of _XXX
     *
     * @param mixed $class
     * @param mixed $fnc
     *
     * @static
     * @access public
     * @return string
     */
    public static function publicize($class, $fnc)
    {
        return self::addFunction($class, preg_replace('/^_/', 'p_', $fnc), "array(\$this, '$fnc')");
    }

    /**
     * clean Ups loaded modules
     *
     * @static
     * @access public
     * @return void
     */
    public static function cleanUp()
    {
        self::$_aModuleMap = array();
        self::$_oOrigOxUtilsObj = null;
        foreach (self::$_addedmods as $class => $arr) {
//            foreach ($arr as $mod) {
            oxRemClassModule('allmods', $class);
            //          }
        }
        self::$_addedmods = array();
    }

    /**
     * cleans every module attached
     */
    public static function cleanAllModules()
    {
        modConfig::getInstance()->setConfigParam('aModules', array());
        oxClassCacheKey(true, "empty");
    }
}

/**
 * creates static cleaner subclasses and nulls parent class protected static property
 */
class oxTestsStaticCleaner
{

    /**
     * get class name
     *
     * @param string $sClass
     *
     * @return string
     */
    protected static function _getChildClass($sClass)
    {
        return __CLASS__ . '_' . $sClass;
    }

    /**
     * create cleaner and execute it
     *
     * @param string $sClass
     * @param string $sProperty
     *
     * @return null
     */
    public static function clean($sClass, $sProperty)
    {
        $sNewCl = self::_getChildClass($sClass);
        if (!class_exists($sNewCl)) {
            eval ("class $sNewCl extends $sClass { public function __construct(){} public function __cleaner(\$sProperty) { $sClass::\${\$sProperty}=null; }}");
        }
        $o = new $sNewCl();
        $o->__cleaner($sProperty);
    }
}

/**
 * adds or replaces oxConfig functionality.
 * [because you just can not use module emulation functionality with oxConfig]
 * usage:
 *     to initialize just create a new instance of the class.
 *  to replace OR attach some oxConfig function use addClassFunction method:
 *  to end with mod, use remClassFunction or just cleanup.
 *
 *   e.g.
 *
 *   Executor
 *     $a = modConfig::getInstance();
 *     $a->addClassFunction('getDB', array($this, 'getMyDb'));
 *
 *   OR
 *
 *   Observer
 *     $a = modConfig::getInstance();
 *     $a->addClassFunction('getDB', array($this, 'countGetDbCalls'), false);
 *
 *
 * this class is also usable to override some oxConfig variable by using
 *     addClassVar function (if second parameter is null [default], the initial value of
 *  overriden variable is the orginal oxConfig's value)
 *
 * Also, since all tests are INDEPENDANT, no real changes are made to the real instance.
 * NOTE: after cleanup, all oxConfig variable changes while modConfig was active are LOST.
 *
 */


abstract class modOXID
{

    protected $_takeover = array();
    protected $_checkover = array();
    protected $_vars = array();
    protected $_params = array();
    protected $_oRealInstance = null;

    public function getRealInstance()
    {
        return $this->_oRealInstance;
    }

    public function modAttach($oObj = null)
    {
        $this->cleanup();
    }

    public function cleanup()
    {
        $this->_takeover = array();
        $this->_checkover = array();
        $this->_vars = array();
        $this->_params = array();

    }

    public static function globalCleanup()
    {
        // cleaning up core info
        $oConfig = new oxsupercfg();
        $oConfig->setConfig(null);
        $oConfig->setSession(null);
        $oConfig->setUser(null);
        $oConfig->setAdminMode(null);

        oxTestModules::cleanAllModules();
    }

    public function addClassFunction($sFunction, $callback, $blTakeOver = true)
    {
        $sFunction = strtolower($sFunction);
        if ($blTakeOver) {
            $this->_takeover[$sFunction] = $callback;
        } else {
            $this->_checkover[$sFunction] = $callback;
        }
    }

    public function remClassFunction($sFunction)
    {
        $sFunction = strtolower($sFunction);
        if (isset($this->_takeover[$sFunction])) {
            unset($this->_takeover[$sFunction]);
        }
        if (isset($this->_checkover[$sFunction])) {
            unset($this->_checkover[$sFunction]);
        }
    }

    public function addClassVar($name, $value = null)
    {
        $this->_vars[$name] = (isset($value)) ? $value : $this->_oRealInstance->$name;
    }

    public function remClassVar($name)
    {
        if (array_key_exists($name, $this->_vars)) {
            unset($this->_vars[$name]);
        }
    }

    public function __call($func, $var)
    {
        $funca = strtolower($func);
        if (isset($this->_takeover[$funca])) {
            return call_user_func_array($this->_takeover[$funca], $var);
        } else {
            if (isset($this->_checkover[$funca])) {
                call_user_func_array($this->_checkover[$funca], $var);
            }

            return call_user_func_array(array($this->_oRealInstance, $func), $var);
        }
    }

    public function __get($nm)
    {
        // maybe should copy var line in __set function ???
        // if it would help to clone object properties...
        if (array_key_exists($nm, $this->_vars)) {
            return $this->_vars[$nm];
        }

        return $this->_oRealInstance->getConfigParam($nm);
    }

    public function __set($nm, $val)
    {
        // this is commented out for the reason:
        // all tests are INDEPENDANT, so no real changes should be made to the real
        // instance.
        // NOTE: after cleanup, all changes to oxConfig while modConfig was active are LOST.
        //        if (array_key_exists($nm, $this->_vars)) {
        $this->_vars[$nm] = $val;
        //            return;
        //        }
        //        $this->_oRealInstance->$nm = &$val;
    }

    public function __isset($nm)
    {
        if (array_key_exists($nm, $this->_vars)) {
            return isset($this->_vars[$nm]);
        }

        return isset($this->_oRealInstance->$nm);
    }

    public function __unset($nm)
    {
        if (array_key_exists($nm, $this->_vars)) {
            $this->_vars[$nm] = null;

            return;
        }
        unset($this->_oRealInstance->$nm);
    }
}

//-----------------
/**
 * config class for test
 *
 * @deprecated
 */
class modConfig extends modOXID
{

    // needed 4 modOXID
    public static $unitMOD = null;
    public static $unitCustMOD = null;
    protected static $_inst = null;
    protected $_aConfigparams = array();

    function modAttach($oObj = null)
    {
        parent::modAttach($oObj);
        self::$unitMOD = null;
        $this->_oRealInstance = oxRegistry::getConfig();
        if (!$oObj) {
            $oObj = $this;
        }
        self::$unitMOD = $oObj;
    }

    /**
     * @return modConfig
     */
    static function getInstance()
    {
        if (!self::$_inst) {
            self::$_inst = new modConfig();
        }
        if (!self::$unitMOD) {
            self::$_inst->modAttach();
        }

        return self::$_inst;
    }

    public function cleanup()
    {
        self::$unitMOD = null;
        self::$unitCustMOD = null;

        // cleaning config parameters
        $this->_aConfigparams = array();

        parent::cleanup();

        if (oxRegistry::getConfig() === $this) {
            throw new Exception("clean config failed");
        }
    }

    public function getModConfigParam($paramName)
    {
        $oInst = self::getInstance();
        if (array_key_exists($paramName, $oInst->_aConfigparams)) {
            return $oInst->_aConfigparams[$paramName];
        }
    }

    public function getConfigParam($paramName)
    {
        $oInst = self::getInstance();
        if (($sValue = $this->getModConfigParam($paramName)) !== null) {
            return $sValue;
        } else {
            if (!$oInst->_oRealInstance) {
                $_i = oxRegistry::getConfig();
                if ($_i instanceof oxConfig) {
                    return $_i->getConfigParam($paramName);
                }
                throw new Exception("real instance is empty!");
            }

            return $oInst->_oRealInstance->getConfigParam($paramName);
        }
    }

    public function isDemoShop()
    {
        $oInst = self::getInstance();
        if (isset($oInst->_aConfigparams['blDemoShop'])) {
            return $oInst->_aConfigparams['blDemoShop'];
        } else {
            return $oInst->_oRealInstance->isDemoShop();
        }
    }

    public function isUtf()
    {
        $oInst = self::getInstance();
        if (isset($oInst->_aConfigparams['iUtfMode'])) {
            return $oInst->_aConfigparams['iUtfMode'];
        } else {
            return $oInst->_oRealInstance->isUtf();
        }
    }

    public function setConfigParam($paramName, $paramValue)
    {
        self::getInstance()->_aConfigparams[$paramName] = $paramValue;
    }

    /**
     * @param      $paramName
     * @param bool $blRaw
     *
     * @deprecated since v5.2.0 (2014-07-02); Use static getRequestParameter()
     *
     * @return string
     */
    static function getParameter($paramName, $blRaw = false)
    {
        return self::getRequestParameter($paramName, $blRaw);
    }

    /**
     * @param $paramName
     * @param $paramValue
     *
     * @deprecated since v5.2.0 (2014-07-02); Use static setRequestParameter()
     */
    static function setParameter($paramName, $paramValue)
    {
        self::setRequestParameter($paramName, $paramValue);
    }

    /**
     * Returns value of parameter stored in POST,GET for tests.
     *
     * @param      $paramName
     * @param bool $blRaw
     *
     * @return string
     */
    public static function getRequestParameter($paramName, $blRaw = false)
    {
        // should throw exception if original functionality is needed.
        if (array_key_exists($paramName, self::getInstance()->_params)) {
            return self::getInstance()->_params[$paramName];
        } else {
            return modSession::getInstance()->getVar($paramName);
        }
    }

    /**
     * Sets request parameter for test.
     *
     * @param $paramName
     * @param $paramValue
     */
    public static function setRequestParameter($paramName, $paramValue)
    {
        // should throw exception if original functionality is needed.
        self::getInstance()->_params[$paramName] = $paramValue;
    }

    /**
     * needed for Erp test where it checks it with method_exists
     *
     */
    public function getSerial()
    {
        return $this->__call('getSerial', array());
    }
}

//-----------------
/**
 * Class modSession
 *
 * @deprecated
 */
class modSession extends modOXID
{

    public static $unitMOD = null;
    public static $unitCustMOD = null;
    protected static $_inst = null;
    protected $_id = null;

    /**
     * Keeps test session vars
     *
     * @var array
     */
    protected $_aSessionVars = array();

    function modAttach($oObj = null)
    {
        parent::modAttach($oObj);
        $this->_oRealInstance = oxRegistry::getSession();
        if (!$oObj) {
            $oObj = $this;
        }
        self::$unitMOD = $oObj;
        $this->_id = $this->_oRealInstance->getId();
    }

    static function getInstance()
    {
        if (!self::$_inst) {
            self::$_inst = new modSession();
        }
        if (!self::$unitMOD) {
            self::$_inst->modAttach();
        }

        return self::$_inst;
    }

    public function cleanup()
    {
        if ($this->_oRealInstance) {
            $this->_oRealInstance->setId($this->_id);
        }
        self::$unitMOD = null;
        self::$unitCustMOD = null;
        parent::cleanup();
        $this->_aSessionVars = array();
    }

    /**
     * Set session var for testing
     *
     * @param string $sVar
     * @param string $sVal
     */
    public function setVar($sVar, $sVal)
    {
        $this->_aSessionVars[$sVar] = $sVal;
    }

    /**
     * Gets session var for testing
     *
     * @param string $sVar
     *
     * @return string
     */
    public function getVar($sVar)
    {
        if (isset($this->_aSessionVars[$sVar])) {
            return $this->_aSessionVars[$sVar];
        }

        return $_SESSION[$sVar];
    }

    /**
     * Session properties getter
     *
     * @param string $nm name of parameter
     *
     * @return mixed
     */
    public function __get($nm)
    {
        // maybe should copy var line in __set function ???
        // if it would help to clone object properties...
        if (array_key_exists($nm, $this->_vars)) {
            return $this->_vars[$nm];
        }

        return $this->_oRealInstance->$nm;
    }
}

//-----------------
/**
 * Class modDB
 *
 * @deprecated
 */
class modDB extends modOXID
{

    // needed 4 modOXID
    public static $unitMOD = null;
    protected static $_inst = null;

    function modAttach($oObj = null)
    {
        parent::modAttach();
        $this->_oRealInstance = oxDb::getDb();
        if (!$oObj) {
            $oObj = $this;
        }
        self::$unitMOD = $oObj;
        modConfig::getInstance()->addClassFunction('getDB', create_function('', 'return modDB::$unitMOD;'));
    }

    static function getInstance()
    {
        if (!self::$_inst) {
            self::$_inst = new modDB();
        }
        if (!self::$unitMOD) {
            self::$_inst->modAttach();
        }

        return self::$_inst;
    }

    public function cleanup()
    {
        modConfig::getInstance()->remClassFunction('getDB');
        self::$unitMOD = null;
        parent::cleanup();
    }
}

//-----------------

// useful for extending getDB()->Execute method
class modResource
{

    public $recordCount = 0;
    public $eof = true;
    public $fields = array();

    function RecordCount()
    {
        if ($this->recordCount) {
            $this->EOF = false;
        } else {
            $this->EOF = true;
        }

        return $this->recordCount;
    }

    function MoveNext()
    {
        if ((--$this->recordCount) == 0) {
            $this->EOF = true;
        }
    }
}

// Stores added objects instances in array
// On cleanup clears all stored instances
// Add modInstances->getInstance()->cleanup() when testing in tearDown()
// to force recreate added objects

//-----------------

class modInstances
{

    protected static $_aInst = array();

    public static function addMod($sModId, $oObject)
    {
        self::$_aInst[strtolower($sModId) . oxClassCacheKey()] = $oObject;
    }

    public static function getMod($sModId)
    {
        //print_r(array_keys(self::$_aInst));
        return self::$_aInst[strtolower($sModId) . oxClassCacheKey()];
    }

    public static function cleanup()
    {
        self::$_aInst = array();
    }
}


// ########################################################################
// ###############  CodeCoverage Executible Lines Generator ###############
// ########################################################################

if (!function_exists('getFileArr')) {
    function getFileArr()
    {
        $sBasePath = oxPATH;
        $sCCarrayDir = oxCCTempDir;

        $aDirBlackList = array(
            '/admin/dtaus',
            '/admin/reports/jpgraph',
            '/admin/wysiwigpro',
            '/core/adodb',
            '/core/openid',
            '/core/adodblite',
            '/core/emailvalidation',
            '/core/ERP',
            '/core/tcpdf',
            '/core/nusoap',
            '/core/phpmailer',
            '/core/smarty',
            '/force_version',
            '/out',
            '/out_ee',
            '/out_pe',
            '/tmp',
            '/core/objects', // TODO: remove after gen import was refactored
        );
        $aFileBlackList = array(
            '/_cc.php',
            '/_version_define.php',
            '/core/oxerpbase.php', // TODO: remove after gen import was refactored
            '/core/oxerpcsv.php', // TODO: remove after gen import was refactored
            '/core/oxerpinterface.php', // TODO: remove after gen import was refactored
            '/core/oxopenidhttpfetcher.php', //third party lib
            '/core/oxopenidgenericconsumer.php', //third party lib
        );
        $aFileWhiteList = array(
            '/core/smarty/plugins/emos.php',
            '/core/smarty/plugins/oxemosadapter.php',
            '/core/smarty/plugins/modifier.oxmultilangassign.php',
            '/core/smarty/plugins/modifier.oxnumberformat.php',
            '/core/smarty/plugins/modifier.oxformdate.php',
            '/core/smarty/plugins/insert.oxid_cmplogin.php',
            '/core/smarty/plugins/insert.oxid_cssmanager.php',
            '/core/smarty/plugins/insert.oxid_newbasketitem.php',
            '/core/smarty/plugins/insert.oxid_nocache.php',
            '/core/smarty/plugins/insert.oxid_tracker.php',
            '/core/smarty/plugins/function.oxmultilang.php',
            '/core/smarty/plugins/function.oxid_include_dynamic.php',
            '/core/smarty/plugins/function.oxcontent.php',
            '/core/smarty/plugins/block.oxhasrights.php',
        );
        $arr = findphp($sBasePath, $aDirBlackList, $aFileBlackList, $aFileWhiteList);

        if ($_ENV['PHP_FILE']) {
            $sTestOnlyFile = basename($_ENV['PHP_FILE']);
            $sTestOnlyFile = preg_replace('/Test.php$/i', '', $sTestOnlyFile);
            $sTestOnlyFile = preg_replace('/.php$/i', '', $sTestOnlyFile);
            foreach ($arr as &$sSerchFile) {
                if (stristr($sSerchFile, $sTestOnlyFile)) {
                    $sTestOnlyFile = $sSerchFile;
                    break;
                }
            }

            return array($sTestOnlyFile => stripCodeLines($arr[array_search($sTestOnlyFile, $arr)], $sCCarrayDir));
        }

        $ret = array();
        foreach ($arr as $file) {
            try {
                $ret[$file] = stripCodeLines($file, $sCCarrayDir);
            } catch (Exception $e) {
                // do not add file here;
                echo '', $e->getMessage(), "\n";
            }
        }

        return $ret;
    }
}

if (!function_exists('replaceDirSeperator')) {
    function replaceDirSeperator($sDir)
    {
        if (DIRECTORY_SEPARATOR == '\\') {
            return str_replace('/', '\\', $sDir);
        }

        return $sDir;
    }
}

if (!function_exists('preparePathArray')) {
    function preparePathArray(&$aPaths, $sBasePath)
    {
        foreach (array_keys($aPaths) as $key) {
            $aPaths[$key] = $sBasePath . $aPaths[$key];
        }
        $aPaths = array_map('replaceDirSeperator', $aPaths);
    }
}

if (!function_exists('findphp')) {
    function findphp($baseDir, $aDirBlackList = array(), $aFileBlackList = array(), $aFileWhiteList = array())
    {
        $baseDir = preg_replace('#/$#', '', $baseDir);
        $baseDir = replaceDirSeperator($baseDir);

        $dirs = array($baseDir);

        preparePathArray($aDirBlackList, $baseDir);
        preparePathArray($aFileBlackList, $baseDir);
        preparePathArray($aFileWhiteList, $baseDir);


        //get directories (do not go to blacklist)
        while (list (, $dir) = each($dirs)) {
            foreach (glob($dir . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR) as $sdir) {
                if (array_search($sdir, $aDirBlackList) === false) {
                    $dirs[] = $sdir;
                }
            }
        }

        // get PHP files form directories
        $aFiles = array();
        foreach ($dirs as $dir) {
            $aFiles = array_merge($aFiles, glob($dir . DIRECTORY_SEPARATOR . "*.php", GLOB_NOSORT));
        }

        //remove files existing in file blacklist
        foreach ($aFileBlackList as $sFile) {
            $iNR = array_search($sFile, $aFiles);
            if ($iNR !== false) {
                unset ($aFiles[$iNR]);
            }
        }
        // add files from white list
        foreach ($aFileWhiteList as $sFile) {
            $aFiles[] = $sFile;
        }

        return $aFiles;
    }
}

if (!function_exists('preg_stripper')) {
    function preg_stripper($matches)
    {
        return preg_replace('/.*/', '', $matches[0]);
    }
}

if (!function_exists('stripCodeLines')) {
    function stripCodeLines($sFile, $sCCarrayDir)
    {
        if (!file_exists($sFile)) {
            throw new Exception("\n" . 'File "' . $sFile . '" does not exists, skipping');
        }


        $sFileContentMD5 = md5_file($sFile);
        $sCCFileName = $sCCarrayDir . md5($sFile) . "." . $sFileContentMD5;
        // delete unneeded files
        $aArray = glob($sCCarrayDir . md5($sFile) . ".*");
        $blFound = false;
        if (count($aArray)) {
            while ($aArray) {
                $sF = array_pop($aArray);
                if (!$blFound && $sF === $sCCFileName) {
                    $blFound = true;
                } else {
                    unlink($sF);
                }
            }
        }
        if (!$blFound) {
            $aFile = file_get_contents($sFile);

            $aFile = str_replace(' ', '', $aFile);
            $aFile = str_replace("\t", '', $aFile);
            $aFile = str_replace("\r", '', $aFile);

            $aFile = preg_replace('#//.*#', '', $aFile);
            $aFile = preg_replace_callback('#/\*.*?\*/#sm', 'preg_stripper', $aFile);

            // for variables
            $aFile = preg_replace('#(public|static|protected|private|var|\{|\}).*;#', '', $aFile);
            //for functions
            $aFile = preg_replace('#(public|static|protected|private|var|\{|\})#', '', $aFile);

            $aFile = preg_replace('#^class.*?$#m', '', $aFile);
            $aFile = preg_replace_callback('/\?>.*?<\?php/sm', 'preg_stripper', $aFile);
            $aFile = preg_replace_callback('/\?>.*?<\?/sm', 'preg_stripper', $aFile);
            $aFile = preg_replace_callback('/\.*?<\?php/sm', 'preg_stripper', $aFile);
            $aFile = preg_replace_callback('/\.*?<\?/sm', 'preg_stripper', $aFile);

            $aFile = preg_replace_callback('/\?>.*/sm', 'preg_stripper', $aFile);

            $aFile = preg_replace('#^\$[a-zA-Z0-9_]+;$#m', '', $aFile);

            $aFile = preg_replace('#^function[a-zA-Z0-9_]+\(.*?\)\{?$#m', '', $aFile);
            $aFile = preg_replace('#.+#', '1', $aFile);

            $aFile = preg_replace('#^$#m', '0', $aFile);
            $aFile = str_replace("\n", '', $aFile);
            $aCC = array();
            for ($i = 0; $i < strlen($aFile); $i++) {
                if ($aFile[$i] === '1') {
                    $aCC[$i + 1] = -1;
                }
            }
            file_put_contents($sCCFileName, serialize($aCC));

            return $aCC;
        } else {
            return unserialize(file_get_contents($sCCFileName));
        }
    }
}
