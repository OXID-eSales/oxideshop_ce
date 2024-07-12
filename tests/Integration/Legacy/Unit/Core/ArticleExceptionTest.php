<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

/**
 * Testing oxArticleException class.
 */
class ArticleExceptionTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Contains a test object of oxarticleexception
     *
     * @var object
     */
    private $_oTestObject;

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
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->_oTestObject = oxNew('oxArticleException', $this->_sMsg);
        $this->_oTestObject->setArticleNr($this->_sArticle);
        $this->_oTestObject->setProductId($this->_sArticle);
    }

    /**
     * Test set/get product id.
     */
    public function testSetProductIdGetProductId()
    {
        $oTestObject = oxNew('oxArticleException', $this->_sMsg);
        $this->assertNull($oTestObject->getProductId());

        $this->_oTestObject->setProductId('xxx');
        $this->assertSame('xxx', $this->_oTestObject->getProductId());
    }

    /**
     * Test set type.
     */
    public function testType()
    {
        $this->assertSame(\OxidEsales\Eshop\Core\Exception\ArticleException::class, $this->_oTestObject::class);
    }

    /**
     * Test set/get article nr.
     */
    public function testSetGetArticleNr()
    {
        $this->assertSame($this->_sArticle, $this->_oTestObject->getArticleNr());
    }

    /**
     * Test set string.
     *
     * We check on class name and message only - rest is not checked yet.
     */
    public function testSetString()
    {
        $sStringOut = $this->_oTestObject->getString();
        $this->assertStringContainsString($this->_sMsg, $sStringOut); // Message
        $this->assertStringContainsString('ArticleException', $sStringOut); // Exception class name
        $this->assertStringContainsString($this->_sArticle, $sStringOut); // Article nr
    }

    /**
     * Test get Values.
     */
    public function testGetValues()
    {
        $aRes = $this->_oTestObject->getValues();
        $this->assertArrayHasKey('articleNr', $aRes);
        $this->assertArrayHasKey('productId', $aRes);
        $this->assertSame($aRes['articleNr'], $this->_sArticle);
        $this->assertSame($aRes['productId'], $this->_sArticle);
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
