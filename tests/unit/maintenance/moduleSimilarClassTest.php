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

class Unit_Maintenance_moduleSimilarClassTest extends OxidTestCase
{

    /**
     * test when overloading class in module with similar name as other module
     */
    public function testModuleSimilarName()
    {
        oxUtilsObject::getInstance()->setModuleVar('aModules', array('oxbasketitem' => 'testbasketitem', 'oxbasket' => 'testbasket'));

        include_once dirname(__FILE__) . '/modules/testbasket.php';

        $oTestMod = oxNew('testbasket');
        $this->assertEquals("Hi!", $oTestMod->sayHi());
    }

    /**
     * test catching exception when calling not existent similar module
     */
    public function testModuleSimilarName_ClassNotExist()
    {
        $this->setExpectedException('oxSystemComponentException');
        modConfig::getInstance()->setConfigParam(
            'aModules', array(
                             'oxbasketitem' => 'test/testbasket')
        );
        $oBask = oxNew('testbaske');
    }
}
