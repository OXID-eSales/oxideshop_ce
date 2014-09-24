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

require_once 'PHPUnit/Framework/TestSuite.php';

echo "=========\nrunning php version " . phpversion() . "\n\n============\n";

/**
 * PHPUnit_Framework_TestCase implementation for adding and testing all unit tests from unit dir
 */
class AllTestsRunner extends PHPUnit_Framework_TestCase
{

    /** @var array Default test suites */
    protected static $_aTestSuites = array();

    /**
     * Returns test files filter
     *
     * @return string
     */
    public static function getTestFileFilter()
    {
        $sTestFileNameEnd = '*[^8]Test.php';
        if (getenv('OXID_TEST_UTF8')) {
            $sTestFileNameEnd = '*utf8Test.php';
        }

        return $sTestFileNameEnd;
    }

    /**
     * Forms test suite
     *
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite()
    {
        $aTestDirectories = static::_getTestDirectories();

        $oSuite = new PHPUnit_Framework_TestSuite('PHPUnit');
        foreach ($aTestDirectories as $sDirectory) {
            $sFilesSelector = "$sDirectory/" . static::getTestFileFilter();
            $aTestFiles = glob($sFilesSelector);

            if (empty($aTestFiles)) {
                continue;
            }

            echo "Adding unit tests from $sFilesSelector\n";

            $oSuite = static::_addFilesToSuite($oSuite, $aTestFiles);
        }

        return $oSuite;
    }

    /**
     * Adds files to test suite
     *
     * @param PHPUnit_Framework_TestSuite $oSuite
     * @param array                       $aTestFiles
     *
     * @return PHPUnit_Framework_TestSuite
     */
    protected static function _addFilesToSuite($oSuite, $aTestFiles)
    {
        foreach ($aTestFiles as $sFilename) {

            $sFilter = getenv('PREG_FILTER');
            if (!$sFilter || preg_match("&$sFilter&i", $sFilename)) {

                include_once $sFilename;
                $sClassName = str_replace(array("/", ".php"), array("_", ""), $sFilename);

                if (class_exists($sClassName)) {
                    $oSuite->addTestSuite($sClassName);
                } else {
                    if (!isset($blThrowException) || $blThrowException) {
                        echo "\n\nFile with wrong class name found!: $sClassName in $sFilename";
                        exit();
                    }
                }
            }
        }

        return $oSuite;
    }

    /**
     * Returns array of directories, which should be tested
     *
     * @return array
     */
    protected static function _getTestDirectories()
    {
        $aTestDirectories = static::$_aTestSuites;

        $sTestDirs = getenv('TEST_DIRS');
        if ($sTestDirs) {
            $aTestDirectories = array();
            foreach (explode(',', $sTestDirs) as $sTestSuiteParts) {
                $aTestDirectories = array_merge($aTestDirectories, static::_getSuiteDirectories($sTestSuiteParts));
            }
        }

        return array_merge($aTestDirectories, static::_getDirectoryTree($aTestDirectories));
    }

    /**
     * Returns test suite directories
     *
     * @param string $sTestSuiteParts
     *
     * @return array
     */
    protected static function _getSuiteDirectories($sTestSuiteParts)
    {
        $aDirectories = array();

        list($sSuiteKey, $sSuiteTests) = explode(':', $sTestSuiteParts);
        if (!empty($sSuiteTests)) {
            foreach (explode('%', $sSuiteTests) as $sSubDirectory) {
                $sSubDirectory = ($sSubDirectory == "_root_") ? "" : '/' . $sSubDirectory;
                $aDirectories[] = "${sSuiteKey}${sSubDirectory}";
            }
        } else {
            $aDirectories[] = $sSuiteKey;
        }

        return $aDirectories;
    }

    /**
     * Scans given tests directories and returns formed directory tree
     *
     * @param array $aDirectories
     *
     * @return array
     */
    protected static function _getDirectoryTree($aDirectories)
    {
        $aTree = array();

        foreach ($aDirectories as $sDirectory) {
            $aTree = array_merge($aTree, array_diff(glob($sDirectory . "/*", GLOB_ONLYDIR), array('.', '..')));
        }

        if (!empty($aTree)) {
            $aTree = array_merge($aTree, static::_getDirectoryTree($aTree));
        }

        return $aTree;
    }

}
