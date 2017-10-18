<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

/**
 * Testing beta note class
 */
class BetaNoteTest extends \OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Provides links and expected links
     *
     * @return array
     */
    public function linkProvider()
    {
        return array(
            array(null, null),
            array('http://testlink', 'http://testlink'),
            array('', '')
        );
    }

    /**
     * @dataProvider linkProvider
     */
    public function testgetBetaNoteLink($sValuetoSet, $sExpected)
    {
        $oBetaNote = oxNew('oxwBetaNote');

        $oBetaNote->setBetaNoteLink($sValuetoSet);

        $this->assertEquals($sExpected, $oBetaNote->getBetaNoteLink());
    }
}
