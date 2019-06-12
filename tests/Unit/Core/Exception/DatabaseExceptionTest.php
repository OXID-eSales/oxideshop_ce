<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Exception;

use OxidEsales\EshopCommunity\Core\Exception\DatabaseErrorException;
use OxidEsales\TestingLibrary\UnitTestCase;

/**
 *
 * Test class for DatabaseException
 *
 * @group database-adapter
 */
class DatabaseExceptionTest extends UnitTestCase
{

    /**
     * DatabaseException must be an instance of oxException
     */
    public function testDatabaseExceptionIsInstanceOfOxException()
    {
        $message = 'message';
        $code = 1;
        $previous = new \Exception();

        $expected = 'oxException';
        $actualException = new DatabaseErrorException($message, $code, $previous);

        $this->assertInstanceOf($expected, $actualException, 'DatabaseException is not an instance of oxException');
    }
}
