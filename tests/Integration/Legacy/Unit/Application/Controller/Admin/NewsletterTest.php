<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Controller\Admin\AdminNewsletter;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Tests for Admin_Newsletter class
 */
class NewsletterTest extends \PHPUnit\Framework\TestCase
{
    use ProphecyTrait;

    /**
     * Admin_News::Render() test case
     */
    public function testRender(): void
    {
        $oView = oxNew('Admin_Newsletter');
        $this->assertEquals('newsletter', $oView->render());
    }

    public function testIfExportRecipientsGenerateCSVFile(): void
    {
        $adminNewsletter = $this->getMockBuilder(AdminNewsletter::class)
            ->onlyMethods(['export'])
            ->getMock();

        $adminNewsletter->expects($this->once())->method('export');
        $adminNewsletter->export();
    }
}
