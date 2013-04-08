<?php
/**
 * This file contains the script required to run all PE edition unit tests in unit dir on Cruise Control.
 * This file is supposed to be executed over PHPUnit framework
 * It is called something like this:
 * phpunit <Test dir>_AllTests
 *
 *
 * LICENSE: This Software is the property of OXID eSales GmbH and is protected
 *          by copyright law - it is NOT Freeware.
 *
 * @copyright   2006 OXID eSales GmbH
 * @version     0.1
 * @since       File available since Version 0.1
 */

require_once 'PHPUnit/Framework/TestSuite.php';
error_reporting( (E_ALL ^ E_NOTICE) | E_STRICT );
ini_set('display_errors', true);

echo "=========\nrunning php version ".phpversion()."\n\n============\n";

/**
 * PHPUnit_Framework_TestCase implemetnation for adding and testing all unit tests from unit dir
 */
class AllTestsUnit extends PHPUnit_Framework_TestCase
{

    static function suite()
    {

        $suite = new PHPUnit_Framework_TestSuite('PHPUnit');

        //adding UNIT Tests
        foreach( glob("unit/*Test.php") as $sFilename) {
            error_reporting( (E_ALL ^ E_NOTICE) | E_STRICT );
            ini_set('display_errors', true);
            require_once( $sFilename);
            $sFilename = "unit_".str_replace("/", "_", str_replace( array( ".php", "unit/"), "", $sFilename));

            if ( class_exists( $sFilename ) ) {
                $suite->addTestSuite( $sFilename );
            } else {
                echo "\n\nWarning: class not found: $sFilename\n\n\n ";
            }
        }

        return $suite;
    }
}
