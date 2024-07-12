<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

/**
 * Testing \OxidEsales\Eshop\Core\Exception\ArticleInputException class
 */
class ArticleinputexceptionTest extends \PHPUnit\Framework\TestCase
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
        $this->assertSame(\OxidEsales\Eshop\Core\Exception\ArticleInputException::class, $testObject::class);
        $articleNumber = 'sArticleNumber';
        $testObject->setArticleNr($articleNumber);
        $stringOut = $testObject->getString();
        $this->assertStringContainsString($msg, $stringOut); // Message
        $this->assertStringContainsString('ArticleInputException', $stringOut); // Exception class name
        $this->assertStringContainsString($articleNumber, $stringOut); // Article nr
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
