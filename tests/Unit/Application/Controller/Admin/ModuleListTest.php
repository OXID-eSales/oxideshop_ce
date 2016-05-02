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
namespace Unit\Application\Controller\Admin;

use oxConfig;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class ModuleListTest extends \OxidTestCase
{
    /**
     * Module_List::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        $oView = oxNew('Module_List');
        $this->assertEquals('module_list.tpl', $oView->render());
    }
    
    public function testRenderWithCorrectModuleNames()
    {
        /** @var oxConfig|MockObject $config */
        $config = $this->getMock('oxConfig', array('getModulesDir'));
        $config->expects($this->any())->method('getModulesDir')->will($this->returnValue(__DIR__.'/../../../testData/modules/'));

        $oView = oxNew('Module_List');
        $oView->setConfig($config);
        $this->assertEquals('module_list.tpl', $oView->render());

        $aViewData = $oView->getViewData();
        $aModulesNames = array_keys($aViewData['mylist']);
        $this->assertSame('testmodule', current($aModulesNames));
    }
}
