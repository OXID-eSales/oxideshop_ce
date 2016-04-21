<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace Unit\Core\Edition;

use OxidEsales\Eshop\Core\Edition\EditionRootPathProvider;
use OxidEsales\Eshop\Core\Edition\EditionSelector;
use OxidEsales\TestingLibrary\UnitTestCase;
use PHPUnit_Framework_MockObject_MockObject;

class EditionRootPathProviderTest extends UnitTestCase
{
    public function providerReturnsEditionPath()
    {
        return array(
            array(EditionSelector::ENTERPRISE, rtrim(getShopBasePath(), '/') . '/Edition/Enterprise/'),
            array(EditionSelector::PROFESSIONAL, rtrim(getShopBasePath(), '/') . '/Edition/Professional/'),
            array(EditionSelector::COMMUNITY, rtrim(getShopBasePath(), '/') . '/'),
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
     * @return PHPUnit_Framework_MockObject_MockObject
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

        $editionSelector = $this->getMockBuilder('OxidEsales\Eshop\Core\Edition\EditionSelector')->getMock();
        $editionSelector->method($mockedMethodName)->willReturn(true);

        return $editionSelector;
    }
}
