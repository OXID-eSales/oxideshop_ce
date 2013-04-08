<?php
/**
 * This Software is the property of OXID eSales and is protected
 * by copyright law - it is NOT Freeware.
 *
 * Any unauthorized use of this software without a valid license key
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 *
 * @link http://www.oxid-esales.com
 * @package package_name
 * @copyright © OXID eSales AG 2003-2008
 */
require_once 'PHPUnit/Framework/TestSuite.php';

/**
 * PHPUnit_Framework_TestCase implemetnation for adding and testing all tests from this dir
 */
class AllTests extends PHPUnit_Framework_TestCase {

    static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('PHPUnit');

        //adding UNIT Tests
        foreach( glob("Unit/*Test.php") as $sFilename) {
            require_once( $sFilename);
            $sFilename = 'Unit_'.str_replace("/", "_", str_replace( array( ".php", 'Unit/'), "", $sFilename));
            $suite->addTestSuite( $sFilename);
        }

        return $suite;
    }

}
