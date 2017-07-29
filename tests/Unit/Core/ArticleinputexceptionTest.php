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
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

/**
 * Testing \OxidEsales\Eshop\Core\Exception\ArticleInputException class
 */
class ArticleinputexceptionTest extends \OxidEsales\TestingLibrary\UnitTestCase
{

    /**
     * Test set string.
     *
     * We check on class name and message only - rest is not checked yet.
     */
    public function testGetString()
    {
        $msg = 'Erik was here..';
        $testObject = oxNew(\OxidEsales\Eshop\Core\Exception\ArticleInputException::class, $msg);
        $this->assertEquals('OxidEsales\Eshop\Core\Exception\ArticleInputException', get_class($testObject));
        $articleNumber = 'sArticleNumber';
        $testObject->setArticleNr($articleNumber);
        $stringOut = $testObject->getString();
        $this->assertContains($msg, $stringOut); // Message
        $this->assertContains('ArticleInputException', $stringOut); // Exception class name
        $this->assertContains($articleNumber, $stringOut); // Article nr
    }

    /**
     * Test type getter.
     */
    public function testGetType()
    {
        $class = 'oxArticleInputException';
        $exception = oxNew($class);
        $this->assertSame($class, $exception->getType());
    }
}
