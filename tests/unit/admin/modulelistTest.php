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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

class Unit_Admin_ModuleListTest extends OxidTestCase
{
    /** @var Module_List */
    private $moduleList;

    public function setUp()
    {
        parent::setup();

        /** @var oxConfig|PHPUnit_Framework_MockObject_MockObject $config */
        $config = $this->getMock('oxConfig', array('getModulesDir'));
        $config->expects($this->any())
            ->method('getModulesDir')
            ->will($this->returnValue(__DIR__.'/../testData/modules/'));
        oxRegistry::set('oxConfig', $config);

        $moduleList = oxNew('Module_List');

        $this->moduleList = $moduleList;
    }

    public function testRenderReturnsCorrectTemplateName()
    {
        $this->assertEquals('module_list.tpl', $this->moduleList->render());
    }

    public function testGetViewDataContainsOurModuleList()
    {
        // Needs to be called since render method triggers the population of viewData.
        $this->moduleList->render();

        $aViewData = $this->moduleList->getViewData();
        $aModulesNames = array_keys($aViewData['mylist']);

        $this->assertSame(array('testmodule'), $aModulesNames);
    }
}
