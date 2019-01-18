<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Setup\Exception;

use OxidEsales\EshopCommunity\Setup\Exception\SetupControllerExitException;

class SetupControllerExitExceptionTest extends \PHPUnit\Framework\TestCase
{
    public function testCanCreateSut()
    {
        new SetupControllerExitException();
    }

    public function testIsThrowable()
    {
        $this->expectException(SetupControllerExitException::class);

        throw new SetupControllerExitException();
    }

    public function testIndicatesWasThrownWithoutArgumentsOfTemplateFile()
    {
        $expectedTemplateFileName = null;

        try {
            throw new SetupControllerExitException();
        } catch (SetupControllerExitException $exception) {
            $actualTemplateFileName = $exception->getTemplateFileName();
        }

        $this->assertSame($expectedTemplateFileName, $actualTemplateFileName);
    }

    public function testIndicatesWasThrownWithArgumentOfTemplateFile()
    {
        $expectedTemplateFileName = 'test.php';

        try {
            throw new SetupControllerExitException($expectedTemplateFileName);
        } catch (SetupControllerExitException $exception) {
            $actualTemplateFileName = $exception->getTemplateFileName();
        }

        $this->assertSame($expectedTemplateFileName, $actualTemplateFileName);
    }
}
