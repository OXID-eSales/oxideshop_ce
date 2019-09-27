<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Install\DataObject;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use PHPUnit\Framework\TestCase;

class OxidEshopPackageTest extends TestCase
{
    public function testGetTargetDirectoryIfCustomDirectoryIdSet()
    {
        $package = $this->getPackage();
        $this->assertSame(
            'customTargetDir',
            $package->getTargetDirectory()
        );
    }

    public function testGetTargetDirectoryReturnPackageNameIfCustomDirectoryIsNotSet()
    {
        $package = new OxidEshopPackage(
            'shinyPackage',
            'pathToPackage'
        );

        $this->assertSame(
            'shinyPackage',
            $package->getTargetDirectory()
        );
    }

    public function testGetPackageSourcePath()
    {
        $package = new OxidEshopPackage(
            'shinyPackage',
            'pathToPackage'
        );

        $this->assertSame(
            'pathToPackage',
            $package->getPackageSourcePath()
        );
    }

    public function testGetPackageSourcePathIfCustomDirectoryIdSet()
    {
        $package = $this->getPackage();
        $this->assertSame(
            'pathToPackage/customSourceDir',
            $package->getPackageSourcePath()
        );
    }

    public function testGetBlackListFilters()
    {
        $package = $this->getPackage();
        $this->assertSame(
            ['blackDir'],
            $package->getBlackListFilters()
        );
    }

    private function getPackage(): OxidEshopPackage
    {
        $package = new OxidEshopPackage(
            'shinyPackage',
            'pathToPackage'
        );
        $package->setTargetDirectory('customTargetDir');
        $package->setBlackListFilters(['blackDir']);
        $package->setSourceDirectory('customSourceDir');

        return $package;
    }
}
