<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

class FileexceptionTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    private $testObject = null;
    private $message = 'Erik was here..';
    private $fileName = 'a file name';
    private $fileError = 'a error text';

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $this->testObject = oxNew(\OxidEsales\Eshop\Core\Exception\FileException::class, $this->message);
        $this->assertEquals(\OxidEsales\Eshop\Core\Exception\FileException::class, get_class($this->testObject));
        $this->testObject->setFileName($this->fileName);
        $this->testObject->setFileError($this->fileError);
    }

    public function testSetGetFileName()
    {
        $this->assertEquals($this->fileName, $this->testObject->getFileName());
    }

    public function testSetGetFileError()
    {
        $this->assertEquals($this->fileError, $this->testObject->getFileError());
    }

    // We check on class name and message only - rest is not checked yet
    public function testGetString()
    {
        $stringOut = $this->testObject->getString();
        $this->assertContains($this->message, $stringOut); // Message
        $this->assertContains('FileException', $stringOut); // Exception class name
        $this->assertContains($this->fileName, $stringOut); // File name
        $this->assertContains($this->fileError, $stringOut); // File error
    }

    public function testGetValues()
    {
        $result = $this->testObject->getValues();
        $this->assertArrayHasKey('fileName', $result);
        $this->assertTrue($this->fileName === $result['fileName']);
    }

    /**
     * Test type getter.
     */
    public function testGetType()
    {
        $class = 'oxFileException';
        $exception = oxNew($class);
        $this->assertSame($class, $exception->getType());
    }
}
