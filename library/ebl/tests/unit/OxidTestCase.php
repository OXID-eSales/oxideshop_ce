<?php


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
//require_once 'test_config.inc.php';
if (!function_exists('oxNew')) {
    function oxNew( $sClassName ) {
        $aParams = func_get_args();
        $iArgCnt = count($aParams);
        // dynamic creation (if parameter count < 4) gives more performance for regular objects
        switch( $iArgCnt ) {
            case 0:
                $oObj = new $sClassName();
                break;
            case 1:
                $oObj = new $sClassName( $aParams[0] );
                break;
            case 2:
                $oObj = new $sClassName( $aParams[0], $aParams[1] );
                break;
            case 3:
                $oObj = new $sClassName( $aParams[0], $aParams[1], $aParams[2] );
                break;
            default:
                try {
                    // unlimited constructor arguments support
                    $oRo = new ReflectionClass( $sClassName );
                    $oObj = $oRo->newInstanceArgs( $aParams );
                } catch ( ReflectionException $oRefExcp ) {
                    // something went wrong?
                    $oEx = oxNew( "oxSystemComponentException" );
                    $oEx->setMessage( $oRefExcp->getMessage() );
                    $oEx->setComponent( $sClassName );
                    $oEx->debugOut();
                    throw $oEx;
                }
        }

        return $oObj;
    }
}

class OxidMockStubFunc implements PHPUnit_Framework_MockObject_Stub
{
    private $_func;

    public function __construct($sFunc)
    {
        $this->_func = $sFunc;
    }

    public function invoke(PHPUnit_Framework_MockObject_Invocation $invocation)
    {
        return call_user_func_array($this->_func, $invocation->parameters);
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

        error_reporting( (E_ALL ^ E_NOTICE) | E_STRICT );
        ini_set('display_errors', true);
    }

    /**
     * Executed after test is donw
     *
     */
    protected function tearDown()
    {
        $_SERVER  = $this->_aBackup['_SERVER'];
        $_POST    = $this->_aBackup['_POST'];
        $_GET     = $this->_aBackup['_GET'];
        $_SESSION = $this->_aBackup['_SESSION'];
        $_COOKIE  = $this->_aBackup['_COOKIE'];

        parent::tearDown();
        //oxTestRegister::execute();
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
     * Create proxy of given class. Proxy allows to test of protected class methods and to access non public members
     *
     * @param string $superClassName
     * @param array|null $constructorParams parameters for contructor
     *
     * @return object
     */
    public function getProxyClass($superClassName, array $params = null)
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

        if (!empty($params)) {
            // Create an instance using Reflection, because constructor has parameters
            $class = new ReflectionClass($proxyClassName);
            $instance = $class->newInstanceArgs($params);
        } else {
            $instance = new $proxyClassName();
        }
        return $instance;
    }

}
