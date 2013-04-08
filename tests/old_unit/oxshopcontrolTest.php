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
 * @link http://www.oxid-esales.com
 * @package tests
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 */


require_once '../unit/OxidTestCase.php';

class ExecuteTestClass extends oxView {
    public $i=1;

    public function addOne()
    {
        $this->i = $this->i+1;
    }

    public function returnOne()
    {
        return "one";
    }
}

class ExecuteTestClassComponent extends oxView  {
    public $i=1;

    public function addComponentOne()
    {
        $this->i = $this->i+1;
    }
}

class Unit_oxshopcontrolTest extends OxidTestCase {

    protected $sOut = null;
    protected $blRun = null;
    protected $sStartClass = "start";

    private $_oShopControl;

    protected function setUp()
    {
        $this->_oShopControl = new oxShopControl();
    }

    protected function tearDown()
    {
        // cleanup
        modConfig::getInstance()->cleanup();
        modSession::getInstance()->cleanup();
        oxRemClassModule('unit_oxshopcontrolTest_oxutils');
    }

    /**
     * shopcontrol process tests
     *
     * A> at the current stage its hard to test, as many external stuff changes behaviour and so on..
     *
    public function testProcess()
    {
        modConfig::setParameter( 'redirected', 1 );

        $sClass    = 'ExecuteTestClass';
        $sFunction = 'yyy';

        $oConfig = $this->getMock( 'oxconfig', array( 'getConfigParam', 'setActiveView' ) );

        $oConfig->expects( $this->at( 0 ) )->method( 'getConfigParam')->will( $this->returnValue( true ) );
        $oConfig->expects( $this->once() )->method( 'setActiveView');

        $oShopControl = $this->getMock( 'oxshopcontrol', array( 'getConfig', 'isAdmin', '_log', '_startMonitor', '_executeFunction', '_executeNewAction' ) );
        $oShopControl->expects( $this->once() )->method( 'getConfig')->will( $this->returnValue( $oConfig ) );
        $oShopControl->expects( $this->any() )->method( 'isAdmin')->will( $this->returnValue( false ) );
        $oShopControl->expects( $this->once() )->method( '_log')->with( $this->equalTo( $sClass ), $this->equalTo( $sFunction ) );
        $oShopControl->expects( $this->once() )->method( '_startMonitor');
        $oShopControl->expects( $this->once() )->method( '_executeFunction')->will( $this->returnValue( 'xxx' ) );
        $oShopControl->expects( $this->once() )->method( '_executeNewAction')->with( $this->equalTo( 'xxx' ) );

        $oShopControl->UNITprocess( $sClass, $sFunction );
    }
    */
}
