<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
