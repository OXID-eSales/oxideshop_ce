<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

class StateTest extends \OxidTestCase
{
    public function testInit()
    {
        $oState = oxNew('oxState');
        $oState->load('AB');
        $this->assertEquals('Alberta', $oState->oxstates__oxtitle->value);
    }

    /**
     * Tests state ID getter by provided code
     */
    public function testGetIdByCode()
    {
        $oState = oxNew('oxState');
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
        $oState = oxNew('oxState');
        $this->assertEquals($sExpected, $oState->getTitleById($sId), $sMsg);
    }
}
