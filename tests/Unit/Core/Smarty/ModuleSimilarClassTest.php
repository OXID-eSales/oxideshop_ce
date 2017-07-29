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
namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Smarty;

use OxidEsales\EshopCommunity\Core\Exception\SystemComponentException;
use \testModuleSimilarName_parent;
use \oxRegistry;
use \oxTestModules;

/**
 * @group module
 * @package Unit\Core\Smarty
 */
class ModuleSimilarClassTest extends \OxidTestCase
{

    /**
     * test when overloading class in module with similar name as other module
     */
    public function testModuleSimilarName()
    {
        $wrapper = $this->getVfsStreamWrapper();
        oxRegistry::get("oxConfigFile")->setVar("sShopDir", $wrapper->getRootPath());
        $wrapper->createStructure(array(
            'modules' => array(
                'testmodulesimilarname.php' => "<?php
                    class testModuleSimilarName extends testModuleSimilarName_parent {
                        public function sayHi() {
                            return \"Hi!\";
                        }
                    }"
            )
        ));

        $extensions = array('oxbasketitem' => 'testmodulesimilarnameitem', 'oxbasket' => 'testmodulesimilarname');
        \OxidEsales\Eshop\Core\Registry::getUtilsObject()->setModuleVar('aModules', $extensions);

        $oTestMod = oxNew('oxBasket');
        $this->assertEquals("Hi!", $oTestMod->sayHi());
    }

    /**
     * test catching exception when calling not existent similar module
     */
    public function testModuleSimilarName_ClassNotExist()
    {
        $this->stubExceptionToNotWriteToLog(SystemComponentException::class, SystemComponentException::class);

        $wrapper = $this->getVfsStreamWrapper();
        oxRegistry::get("oxConfigFile")->setVar("sShopDir", $wrapper->getRootPath());
        $wrapper->createStructure(array(
            'modules' => array(
                'testmodulesimilarname.php' => "<?php
                    class testModuleSimilarName extends testModuleSimilarName_parent {
                        public function sayHi() {
                            return \"Hi!\";
                        }
                    }"
            )
        ));

        /**
         * Real error handling on missing files is disabled for the tests, but when the shop tries to include that not
         * existing file we expect an error to be thrown
         */
        $this->setExpectedException(\PHPUnit_Framework_Error_Warning::class);

        $extensions = array('oxbasketitem' => 'testmodulesimilar', 'oxbasket' => 'testmodulesimilarname');
        \OxidEsales\Eshop\Core\Registry::getUtilsObject()->setModuleVar('aModules', $extensions);

        oxNew('testmodulesimilar');
    }
}
