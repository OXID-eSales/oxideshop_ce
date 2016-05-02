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

use \testModuleInclusion_parent;
use \oxRegistry;

/**
 * test for situation:
 * module class is registered for oxnew but was not yet instantialized
 * module file inclusion makes autoload mod_parent by including the same module file
 * thus in the end module class is created twice resulting in php fatal error
 */
class ModuleFileInclusionTest extends \OxidTestCase
{

    /**
     * test main scenario
     */
    public function testModuleInclusion()
    {
        $filePath = $this->createFile('testModuleInclusion.php', '<?php
            class testModuleInclusion extends testModuleInclusion_parent {
                public function sayHi() {
                    return "Hi!";
                }
            }
        ');

        oxRegistry::get('oxUtilsObject')->setModuleVar('aModules', array('oxarticle' => 'testmoduleinclusion'));

        include_once $filePath;

        $oTestMod = oxNew('testModuleInclusion');
        $this->assertEquals("Hi!", $oTestMod->sayHi());

        $oTestArt = oxNew('oxArticle');
        $this->assertEquals("Hi!", $oTestArt->sayHi());
    }

    /**
     * test main scenario
     */
    public function testMissingModuleInChain()
    {
        $filePath = $this->createFile('testModuleInclusion.php', '<?php
            class testModuleInclusion extends testModuleInclusion_parent {
                public function sayHi() {
                    return "Hi!";
                }
            }
        ');

        oxRegistry::get('oxUtilsObject')->setModuleVar('aModules', array('oxarticle' => 'testmod2&testmoduleinclusion'));

        include_once $filePath;

        $oTestArt = oxNew('oxArticle');
        $this->assertEquals("Hi!", $oTestArt->sayHi());
    }
}
