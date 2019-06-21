<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

class LanguageexceptionTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    private $testObject = null;
    private $message = 'Erik was here..';
    private $languageConstant = 'a language';

    /**
     * Initialize the fixture.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->testObject = oxNew(\OxidEsales\Eshop\Core\Exception\LanguageException::class, $this->message);
        $this->assertEquals(\OxidEsales\Eshop\Core\Exception\LanguageException::class, get_class($this->testObject));
        $this->testObject->setLangConstant($this->languageConstant);
    }

    public function testSetGetLangConstant()
    {
        $this->assertEquals($this->languageConstant, $this->testObject->getLangConstant());
    }

    // We check on class name and message only - rest is not checked yet
    public function testGetString()
    {
        $sStringOut = $this->testObject->getString();
        $this->assertContains($this->message, $sStringOut); // Message
        $this->assertContains('LanguageException', $sStringOut); // Exception class name
        $this->assertContains($this->languageConstant, $sStringOut); // Language constant
    }

    public function testGetValues()
    {
        $result = $this->testObject->getValues();
        $this->assertArrayHasKey('langConstant', $result);
        $this->assertTrue($this->languageConstant === $result['langConstant']);
    }

    /**
     * Test type getter.
     */
    public function testGetType()
    {
        $class = 'oxLanguageException';
        $exception = oxNew($class);
        $this->assertSame($class, $exception->getType());
    }
}
