<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Edition;

use OxidEsales\EshopCommunity\Core\Edition\EditionRootPathProvider;
use OxidEsales\EshopCommunity\Core\Edition\EditionSelector;
use OxidEsales\TestingLibrary\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class EditionRootPathProviderTest extends UnitTestCase
{
    public function providerReturnsEditionPath()
    {
        $vendorDirectory = VENDOR_PATH;
        return array(
            array(EditionSelector::ENTERPRISE, realpath("$vendorDirectory/oxid-esales/oxideshop-ee").'/'),
            array(EditionSelector::PROFESSIONAL, realpath("$vendorDirectory/oxid-esales/oxideshop-pe/").'/'),
            array(EditionSelector::COMMUNITY, realpath(getShopBasePath()) . '/'),
        );
    }

    /**
     * @param string $edition
     * @param string $setupPath
     *
     * @dataProvider providerReturnsEditionPath
     */
    public function testReturnsEditionPath($edition, $setupPath)
    {
        $editionSelector = $this->getEditionSelectorMock($edition);
        $editionPathSelector = new EditionRootPathProvider($editionSelector);

        $this->assertSame($setupPath, $editionPathSelector->getDirectoryPath());
    }

    /**
     * @param $edition
     * @return PHPUnit\Framework\MockObject\MockObject
     */
    protected function getEditionSelectorMock($edition)
    {
        $mockedMethodName = 'isCommunity';
        if ($edition === EditionSelector::ENTERPRISE) {
            $mockedMethodName = 'isEnterprise';
        }
        if ($edition === EditionSelector::PROFESSIONAL) {
            $mockedMethodName = 'isProfessional';
        }

        $editionSelector = $this->getMockBuilder('OxidEsales\EshopCommunity\Core\Edition\EditionSelector')->getMock();
        $editionSelector->method($mockedMethodName)->willReturn(true);

        return $editionSelector;
    }
}
