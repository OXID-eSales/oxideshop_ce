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

use \testModuleInclusion_parent;
use \oxRegistry;

/**
 * test for situation:
 * module class is registered for oxnew but was not yet instantialized
 * module file inclusion makes autoload mod_parent by including the same module file
 * thus in the end module class is created twice resulting in php fatal error
 *
 * @group module
 */
class ModuleFileInclusionTest extends \OxidTestCase
{

    /**
     * test main scenario
     */
    public function testModuleInclusion()
    {
        $wrapper = $this->getVfsStreamWrapper();
        oxRegistry::get("oxConfigFile")->setVar("sShopDir", $wrapper->getRootPath());
        $wrapper->createStructure(array(
            'modules' => array(
                'testmoduleinclusion.php' => "<?php
                    class testmoduleinclusion extends testmoduleinclusion_parent {
                        public function sayHi() {
                            return \"Hi!\";
                        }
                    }"
            )
        ));

        \OxidEsales\Eshop\Core\Registry::getUtilsObject()->setModuleVar('aModules', array('oxarticle' => 'testmoduleinclusion'));

        $oTestMod = oxNew('testModuleInclusion');
        $this->assertEquals("Hi!", $oTestMod->sayHi());

        $oTestArt = oxNew('oxArticle');
        $this->assertEquals("Hi!", $oTestArt->sayHi());
    }
}
