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
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
 */
namespace Unit\Core\Smarty;

use \testModuleSimilarName_parent;
use \oxRegistry;

class ModuleSimilarClassTest extends \OxidTestCase
{

    /**
     * test when overloading class in module with similar name as other module
     */
    public function testModuleSimilarName()
    {
        $filePath = $this->createFile('testModuleSimilarName.php', '<?php
            class testModuleSimilarName extends testModuleSimilarName_parent {
                public function sayHi() {
                    return "Hi!";
                }
            }
        ');

        $extensions = array('oxbasketitem' => 'testmodulesimilarnameitem', 'oxbasket' => 'testmodulesimilarname');
        oxRegistry::get('oxUtilsObject')->setModuleVar('aModules', $extensions);

        include_once $filePath;

        $oTestMod = oxNew('oxBasket');
        $this->assertEquals("Hi!", $oTestMod->sayHi());
    }

    /**
     * test catching exception when calling not existent similar module
     */
    public function testModuleSimilarName_ClassNotExist()
    {
        $filePath = $this->createFile('testModuleSimilarName.php', '<?php
            class testModuleSimilarName extends testModuleSimilarName_parent {
                public function sayHi() {
                    return "Hi!";
                }
            }
        ');

        $this->setExpectedException('oxSystemComponentException');

        $extensions = array('oxbasketitem' => 'testmodulesimilar', 'oxbasket' => 'testmodulesimilarname');
        oxRegistry::get('oxUtilsObject')->setModuleVar('aModules', $extensions);

        include_once $filePath;

        oxNew('testmodulesimilar');
    }
}
