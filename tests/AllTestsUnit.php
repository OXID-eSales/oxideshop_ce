<?php
/**
 * This file contains the script required to run all PE edition unit tests in unit dir on Cruise Control.
 * This file is supposed to be executed over PHPUnit framework
 * It is called something like this:
 * phpunit <Test dir>_AllTests
 *
 * @link          http://www.oxid-esales.com
 * @copyright (c) OXID eSales AG 2003-#OXID_VERSION_YEAR#
 * @version       SVN: $Id: $
 */

require_once 'AllTestsRunner.php';

/**
 * PHPUnit_Framework_TestCase implementation for running all unit tests from unit dir
 */
class AllTestsUnit extends AllTestsRunner
{

    /** @var array Default test suites */
    protected static $_aTestSuites = array('unit', 'integration');
}
