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

class Unit_Core_oxstateTest extends OxidTestCase
{

    public function testInit()
    {
        $oState = new oxState();
        $oState->load('AB');
        $this->assertEquals('Alberta', $oState->oxstates__oxtitle->value);
    }

    /**
     * Tests state ID getter by provided code
     */
    public function testGetIdByCode()
    {
        $oState = new oxState();
        $this->assertEquals('MB', $oState->getIdByCode('MB', '8f241f11095649d18.02676059'));
    }

    /**
     * Data provider for testGetTitleById
     *
     * @return array
     */
    public function providerStateIDs()
    {
        $sMsgCorrect = 'State title is correct';
        $sMsgEmptyString = 'Empty string is returned';

        $iStateId = 'CA';
        $sStateId = 'AK';

        $sStateTitle = 'Kalifornien';
        $sAltStateTitle = 'Alaska';

        $sWrongId1 = null;
        $sWrongId2 = '';
        $sWrongId3 = 's4';

        $sEmptyString = '';

        return array(
            /*     ID          expected         message         */
            array($iStateId, $sStateTitle, $sMsgCorrect),
            array($sStateId, $sAltStateTitle, $sMsgCorrect),
            array($sWrongId1, $sEmptyString, $sMsgEmptyString),
            array($sWrongId2, $sEmptyString, $sMsgEmptyString),
            array($sWrongId3, $sEmptyString, $sMsgEmptyString)
        );
    }

    /**
     * Testing getTitleById with various IDs passed
     *
     * @dataProvider providerStateIDs
     */
    public function testGetTitleById($sId, $sExpected, $sMsg)
    {
        $oState = new oxState();
        $this->assertEquals($sExpected, $oState->getTitleById($sId), $sMsg);
    }

}