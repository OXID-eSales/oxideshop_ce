<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Setup\Exception;

use OxidEsales\EshopCommunity\Setup\Exception\TemplateNotFoundException;

class TemplateNotFoundExceptionTest extends \PHPUnit\Framework\TestCase
{
    public function testCanCreateSut()
    {
        new TemplateNotFoundException('template_file.php');
    }

    public function testProvidesInformationAboutMissingFile()
    {
        $this->expectException(
            TemplateNotFoundException::class,
            "Template named 'template_file.php' was not found."
        );

        throw new TemplateNotFoundException('template_file.php');
    }
}
