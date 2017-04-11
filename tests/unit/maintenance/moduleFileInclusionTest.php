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

/**
 * test for situation:
 * module class is registered for oxnew but was not yet instantialized
 * module file inclusion makes autoload mod_parent by including the same module file
 * thus in the end module class is created twice resulting in php fatal error
 */
class Unit_Maintenance_moduleFileInclusionTest extends OxidTestCase
{

    /**
     * test main scenario
     */
    public function testModuleInclusion()
    {
        oxUtilsObject::getInstance()->setModuleVar('aModules', array('oxarticle' => 'testmod'));

        include_once dirname(__FILE__) . '/modules/testmod.php';

        $oTestMod = oxNew('testmod');
        $this->assertEquals("Hi!", $oTestMod->sayHi());

        //the folowing line whoich acts as double declaration is not required after #4301 is fixed
        oxUtilsObject::getInstance()->setModuleVar('aModules', array('oxarticle' => 'testmod'));

        $oTestArt = oxNew('oxarticle');
        $this->assertEquals("Hi!", $oTestArt->sayHi());
    }

    /**
     * test main scenario
     */
    public function testMissingModuleInChain()
    {
        oxUtilsObject::getInstance()->setModuleVar('aModules', array('oxarticle' => 'testmod2&testmod'));

        $oTestArt = oxNew('oxarticle');
        $this->assertEquals("Hi!", $oTestArt->sayHi());
    }
}
