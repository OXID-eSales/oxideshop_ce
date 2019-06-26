<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance;

abstract class AdminTestCase extends AcceptanceTestCase
{
    /**
     * Restores database after every test.
     */
    protected function tearDown()
    {
        $this->waitForFrameToLoad('basefrm');
        parent::tearDown();
    }

    /**
     * Assert that the given text is in the table at the wished position.
     *
     * @param string $expectedText The text, that should be at the given position.
     * @param int    $row          The table row, in which the expected text should be.
     * @param int    $column       The table column, in which the expected text should be.
     */
    protected function assertTextExistsAtTablePosition($expectedText, $row, $column)
    {
        $this->assertEquals($expectedText, $this->getText("//tr[@id='row.${row}']/td[${column}]"));
    }
}
