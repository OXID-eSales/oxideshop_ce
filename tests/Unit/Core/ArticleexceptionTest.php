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
 * Testing oxArticleException class.
 */
class ArticleexceptionTest extends \OxidTestCase
{

    /**
     * Contains a test object of oxarticleexception
     *
     * @var object
     */
    private $_oTestObject = null;

    /**
     * a mock message
     *
     * @var string
     */
    private $_sMsg = 'Erik was here..';

    /**
     * a mock article number
     *
     * @var string
     */
    private $_sArticle = 'sArticleNumber';

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_oTestObject = oxNew('oxArticleException', $this->_sMsg);
        $this->_oTestObject->setArticleNr($this->_sArticle);
        $this->_oTestObject->setProductId($this->_sArticle);
    }

    /**
     * Test set/get product id.
     *
     * @return null
     */
    public function testSetProductIdGetProductId()
    {
        $oTestObject = oxNew('oxArticleException', $this->_sMsg);
        $this->assertNull($oTestObject->getProductId());

        $this->_oTestObject->setProductId('xxx');
        $this->assertEquals('xxx', $this->_oTestObject->getProductId());
    }

    /**
     * Test set type.
     *
     * @return null
     */
    public function testType()
    {
        $this->assertEquals('OxidEsales\EshopCommunity\Core\Exception\ArticleException', get_class($this->_oTestObject));
    }

    /**
     * Test set/get article nr.
     *
     * @return null
     */
    public function testSetGetArticleNr()
    {
        $this->assertEquals($this->_sArticle, $this->_oTestObject->getArticleNr());
    }

    /**
     * Test set string.
     *
     * We check on class name and message only - rest is not checked yet.
     *
     * @return null
     */
    public function testSetString()
    {
        $sStringOut = $this->_oTestObject->getString();
        $this->assertContains($this->_sMsg, $sStringOut); // Message
        $this->assertContains('ArticleException', $sStringOut); // Exception class name
        $this->assertContains($this->_sArticle, $sStringOut); // Article nr
    }

    /**
     * Test get Values.
     *
     * @return null
     */
    public function testGetValues()
    {
        $aRes = $this->_oTestObject->getValues();
        $this->assertArrayHasKey('articleNr', $aRes);
        $this->assertArrayHasKey('productId', $aRes);
        $this->assertTrue($this->_sArticle === $aRes['articleNr']);
        $this->assertTrue($this->_sArticle === $aRes['productId']);
    }

    /**
     * Test type getter.
     */
    public function testGetType()
    {
        $class = 'oxArticleException';
        $exception = oxNew($class);
        $this->assertSame($class, $exception->getType());
    }
}
