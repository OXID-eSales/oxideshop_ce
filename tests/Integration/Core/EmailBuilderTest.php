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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
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
