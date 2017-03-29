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

/**
 * @covers oxServerChecker
 */
class Unit_Core_oxServerCheckerTest extends OxidTestCase
{

    public function tearDown()
    {
        parent::tearDown();
        $oUtilsDate = new oxUtilsDate();
        oxRegistry::set('oxUtilsDate', $oUtilsDate);
    }

    public function providerCheckWhenNodeIsValid()
    {
        $iNodeCreationTime = 1400000000;
        $iWhenNodeIsStillValid = $iNodeCreationTime + 11 * 60 * 60;

        return array(
            // When node time is the same as current time.
            array($iNodeCreationTime),
            // When server node is still valid.
            array($iWhenNodeIsStillValid)
        );
    }

    /**
     * @param $iCurrentTime
     *
     * @dataProvider providerCheckWhenNodeIsValid
     */
    public function testCheckWhenNodeIsValid($iCurrentTime)
    {
        $this->_prepareCurrentTime($iCurrentTime);
        $oServerNodeChecker = new oxServerChecker();

        $this->assertTrue($oServerNodeChecker->check($this->_getMockedNode()), 'Server node must be valid.');
    }

    public function providerCheckIfNodeIsNotValid()
    {
        $iNodeCreationTime = 1400000000;
        $iExactTimeWhenNodeIsNotValid = $iNodeCreationTime + 24 * 60 * 60;
        $iTimeWhenServerNodeIsNotValid = $iNodeCreationTime + 25 * 60 * 60;
        $iServerTimeIsSmallerThanNodeTime = $iNodeCreationTime - 1;

        return array(
            // Exact time when server node is not valid.
            array($iExactTimeWhenNodeIsNotValid),
            // Time when node is not valid.
            array($iTimeWhenServerNodeIsNotValid),
            // When server time is smaller then node time.
            array($iServerTimeIsSmallerThanNodeTime),
        );
    }

    /**
     * @param $iCurrentTime
     *
     * @dataProvider providerCheckIfNodeIsNotValid
     */
    public function testCheckIfNodeIsNotValid($iCurrentTime)
    {
        $this->_prepareCurrentTime($iCurrentTime);
        $oServerNodeChecker = new oxServerChecker();

        $this->assertFalse($oServerNodeChecker->check($this->_getMockedNode()), 'Server node must be not valid.');
    }

    public function testCheckWhenNodeDoesNotReturnTimestamp()
    {
        $oServerNodeChecker = new oxServerChecker();
        /** @var oxApplicationServer $oNode */
        $oNode = $this->getMock('oxApplicationServer', array('getTimestamp'));
        $oNode->expects($this->any())->method('getTimestamp')->will($this->returnValue(null));

        $this->assertFalse($oServerNodeChecker->check($oNode), 'Server node must be not valid when returns timestamp null.');
    }

    /**
     * @param int $iCurrentTime
     */
    private function _prepareCurrentTime($iCurrentTime)
    {
        $oUtilsDate = $this->getMock('oxUtilsDate', array('getTime'));
        $oUtilsDate->expects($this->any())->method('getTime')->will($this->returnValue($iCurrentTime));
        /** @var oxUtilsDate $oUtils */
        oxRegistry::set('oxUtilsDate', $oUtilsDate);
    }

    /**
     * @return oxApplicationServer
     */
    private function _getMockedNode()
    {
        /** @var oxApplicationServer $oNode */
        $oNode = $this->getMock('oxApplicationServer', array('getTimestamp'));
        $oNode->expects($this->any())->method('getTimestamp')->will($this->returnValue(1400000000));

        return $oNode;
    }
}