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
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
 */
namespace Unit\Core;

/**
 * Testing oxArticleInputException class
 */
class ArticleinputexceptionTest extends \OxidTestCase
{

    /**
     * Test set string.
     *
     * We check on class name and message only - rest is not checked yet.
     *
     * @return null
     */
    public function testGetString()
    {
        $sMsg = 'Erik was here..';
        $oTestObject = oxNew('oxArticleInputException', $sMsg);
        $this->assertEquals('OxidEsales\EshopCommunity\Core\Exception\ArticleInputException', get_class($oTestObject));
        $sArticle = 'sArticleNumber';
        $oTestObject->setArticleNr($sArticle);
        $sStringOut = $oTestObject->getString();
        $this->assertContains($sMsg, $sStringOut); // Message
        $this->assertContains('ArticleInputException', $sStringOut); // Exception class name
        $this->assertContains($sArticle, $sStringOut); // Article nr
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
