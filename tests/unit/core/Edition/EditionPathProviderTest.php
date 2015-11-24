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

use OxidEsales\Eshop\Core\Edition\EditionPathProvider;

class EditionPathProviderTest extends OxidEsales\TestingLibrary\UnitTestCase
{
    public function testReturnsSetupPath()
    {
        $editionPathSelector = $this->getEditionPathSelectorMock();
        $editionPathSelector = new EditionPathProvider($editionPathSelector);

        $this->assertSame('/Setup/', $editionPathSelector->getSetupDirectoryPath());
    }

    public function testReturnsSqlDirectory()
    {
        $editionSelector = $this->getEditionPathSelectorMock();
        $editionPathSelector = new EditionPathProvider($editionSelector);

        $this->assertSame('/Setup/Sql/', $editionPathSelector->getDatabaseSqlDirectoryPath());
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function getEditionPathSelectorMock()
    {
        $editionSelector = $this->getMockBuilder('EditionSelector')->setMethods(array('getDirectoryPath'))->getMock();
        $editionSelector->method('getDirectoryPath')->willReturn('/');
        return $editionSelector;
    }
}
