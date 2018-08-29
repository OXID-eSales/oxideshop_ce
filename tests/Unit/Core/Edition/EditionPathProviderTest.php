<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Edition;

use OxidEsales\EshopCommunity\Core\Edition\EditionPathProvider;
use OxidEsales\EshopCommunity\Core\Edition\EditionRootPathProvider;
use OxidEsales\TestingLibrary\UnitTestCase;
use PHPUnit_Framework_MockObject_MockObject;

class EditionPathProviderTest extends UnitTestCase
{
    public function testReturnsSetupPath()
    {
        $editionPathSelector = $this->getEditionPathSelectorMock();
        $editionPathSelector = new EditionPathProvider($editionPathSelector);

        $this->assertSame('/Setup/', $editionPathSelector->getSetupDirectory());
    }

    public function testReturnsSqlDirectory()
    {
        $editionSelector = $this->getEditionPathSelectorMock();
        $editionPathSelector = new EditionPathProvider($editionSelector);

        $this->assertSame('/Setup/Sql/', $editionPathSelector->getDatabaseSqlDirectory());
    }

    public function testReturnsSmartyPluginsDirectory()
    {
        $editionSelector = $this->getEditionPathSelectorMock();
        $editionPathSelector = new EditionPathProvider($editionSelector);

        $this->assertSame('/Core/Smarty/Plugin/', $editionPathSelector->getSmartyPluginsDirectory());
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|EditionRootPathProvider
     */
    protected function getEditionPathSelectorMock()
    {
        $editionSelector = $this->getMockBuilder('EditionRootPathProvider')->setMethods(array('getDirectoryPath'))->getMock();
        $editionSelector->method('getDirectoryPath')->willReturn('/');
        return $editionSelector;
    }
}
