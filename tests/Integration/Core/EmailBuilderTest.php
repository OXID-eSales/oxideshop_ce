<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use OxidEsales\Eshop\Core\Email;
use OxidEsales\Eshop\Core\EmailBuilder;
use OxidEsales\TestingLibrary\UnitTestCase;

/**
 * Class EmailBuilderTest
 *
 * @covers \OxidEsales\EshopCommunity\Core\EmailBuilder
 */
class EmailBuilderTest extends UnitTestCase
{
    public function testBuildResultObjectType()
    {
        /** @var EmailBuilder $emailBuilderMock */
        $emailBuilderMock = $this->getMockForAbstractClass(EmailBuilder::class);
        $result = $emailBuilderMock->build();
        $this->assertInstanceOf(Email::class, $result);
    }

    public function testBuildResultContents()
    {
        /** @var EmailBuilder $emailBuilderMock */
        $emailBuilderMock = $this->getMockForAbstractClass(EmailBuilder::class);
        $result = $emailBuilderMock->build();

        // shop info email from test demodata
        $email = 'info@myoxideshop.com';

        $this->assertEquals($result->getRecipient(), [[$email, null]]);
        $this->assertEquals($result->getFrom(), $email);
        $this->assertEquals($result->getSubject(), '');
        $this->assertEquals($result->getBody(), '');
    }
}
