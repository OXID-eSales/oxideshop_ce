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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Util/Filter.php';
require_once 'PHPUnit/Util/Printer.php';
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'dbRestore.php';

class oxPrinter extends PHPUnit_TextUI_ResultPrinter
{
    private $_iDBChangeMode;
    private $_iDBChangeOutput;
    private $_blDBResetPerSuit;
    private $_blDBResetPerTest;

    private $_timeStats;

    private $_oDbRestore;

    public function __construct($_blDBResetPerTest = true, $_blDBResetPerSuit = true, $iDBChangeMode = MAINTENANCE_SINGLEROWS, $_iDBChangeOutput = MAINTENANCE_MODE_ONLYRESET, $blVerbose = false)
    {
        parent::__construct ( null, (bool)$blVerbose );
        $this->_iDBChangeMode = $iDBChangeMode;
        $this->_iDBChangeOutput = $_iDBChangeOutput;
        $this->_blDBResetPerTest = $_blDBResetPerTest;
        $this->_blDBResetPerSuit = $_blDBResetPerSuit;
        $this->_oDbRestore = new DbRestore();

    }

    public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        if ($this->verbose) {
            echo "        ERROR: '" . $e->getMessage() . "'\n".$e->getTraceAsString();
        }
        parent::addError ( $test, $e, $time );
        if ($this->_blDBResetPerTest && ! isset ( $test->blNoDbResetAfterTest )) {
            $this->_oDbRestore->restoreDB ( $this->_iDBChangeMode, $this->_iDBChangeOutput );
            echo ("|");
        }
    }

    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        if ($this->verbose) {
            echo "        FAIL: '" . $e->getMessage() . "'\n".$e->getTraceAsString();
        }
        parent::addFailure ( $test, $e, $time );
        if ($this->_blDBResetPerTest && ! isset ( $test->blNoDbResetAfterTest )) {
            $this->_oDbRestore->restoreDB ( $this->_iDBChangeMode, $this->_iDBChangeOutput );
            echo ("|");
        }
    }

    public function endTest(PHPUnit_Framework_Test $test, $time)
    {

        $t = microtime(true) - $this->_timeStats['startTime'];
        if ($this->_timeStats['min'] > $t) {
            $this->_timeStats['min'] = $t;
        }
        if ($this->_timeStats['max'] < $t) {
            $this->_timeStats['max'] = $t;
            $this->_timeStats['slowest'] = $test->getName();
        }
        $this->_timeStats['avg'] = ($t + $this->_timeStats['avg']*$this->_timeStats['cnt']) / (++$this->_timeStats['cnt']);

        parent::endTest ( $test, $time );
        if ($this->_blDBResetPerTest && ! isset ( $test->blNoDbResetAfterTest )) {
            $this->_oDbRestore->restoreDB ( $this->_iDBChangeMode, $this->_iDBChangeOutput );
            echo ("|");
        }
    }

    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        parent::endTestSuite ( $suite );
        if ($this->_blDBResetPerSuit) {
            $this->_oDbRestore->restoreDB ( $this->_iDBChangeMode, $this->_iDBChangeOutput );
            echo ("|");
        }

        echo "\ntime stats: min {$this->_timeStats['min']}, max {$this->_timeStats['max']}, avg {$this->_timeStats['avg']}, slowest test: {$this->_timeStats['slowest']}|\n";
    }

    public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        echo ("\n\n" . $suite->getName () . "\n");

        $this->_timeStats = array('cnt' => 0, 'min' => 9999999, 'max' => 0, 'avg' => 0, 'startTime' => 0, 'slowest' => '_ERROR_');

        parent::startTestSuite ( $suite );
    }

    /**
     * A test started.
     *
     * @param  PHPUnit_Framework_Test $test
     * @access public
     */
    public function startTest(PHPUnit_Framework_Test $test)
    {
        if ($this->verbose) {
            echo "\n        " . $test->getName ();
        }

        $this->_timeStats['startTime'] = microtime(true);

        parent::startTest ( $test );
    }
}

?>
