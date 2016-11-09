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
namespace Integration\Modules;

use OxidEsales\EshopCommunity\Core\Registry;

require_once __DIR__ . '/BaseModuleTestCase.php';

/**
 * Class VirtualNameSpaceClassMapTest
 *
 * @package Integration\Modules
 */
class VirtualNameSpaceClassMapTest extends BaseModuleTestCase
{

    /**
     * @var Environment The helper object for the environment.
     */
    protected $environment = null;

    /**
     * Standard set up method. Calls parent first.
     */
    public function setUp()
    {
        parent::setUp();

        $configFile = Registry::get("oxConfigFile");
        $configFile->setVar('sShopDir', realpath(__DIR__ . '/TestData'));

        Registry::set('oxConfigFile', $configFile);

        $this->environment = new Environment();
    }

    /**
     * Standard tear down method. Calls parent last.
     */
    public function tearDown()
    {
        $this->environment->clean();

        parent::setUp();
    }

    /**
     * Data provider for the method testVirtualNamespaceModules.
     *
     * @return array The inputs for the method testVirtualNamespaceModules.
     */
    public function dataProviderForTestVirtualNamespaceModules()
    {
        return array(
            array(
                'modulesToActivate'        => array('virtualnamespace_module1'),
                'expectedInheritanceChain' => array(
                    'Test1ContentController',
                    'OxidEsales\EshopCommunity\Application\Controller\ContentController',
                    'oxUBase',
                    'OxidEsales\EshopCommunity\Application\Controller\FrontendController',
                    'oxView',
                    'OxidEsales\EshopCommunity\Core\Controller\BaseController',
                    'oxSuperCfg',
                    'OxidEsales\EshopCommunity\Core\Base'
                ),
                'expectedTitle'            => 'Impressum - Module_1_Controller - Module_1_Model'
            ),
            array(
                'modulesToActivate'        => array('virtualnamespace_module1', 'virtualnamespace_module2'),
                'expectedInheritanceChain' => array(
                    'Test2ContentController',
                    'Test1ContentController',
                    'OxidEsales\EshopCommunity\Application\Controller\ContentController',
                    'oxUBase',
                    'OxidEsales\EshopCommunity\Application\Controller\FrontendController',
                    'oxView',
                    'OxidEsales\EshopCommunity\Core\Controller\BaseController',
                    'oxSuperCfg',
                    'OxidEsales\EshopCommunity\Core\Base'
                ),
                'expectedTitle'            => 'Impressum - Module_1_Controller - Module_1_Model - Module_2_Controller'
            ),
            array(
                'modulesToActivate'        => array('virtualnamespace_module1', 'virtualnamespace_module2', 'virtualnamespace_module3'),
                'expectedInheritanceChain' => array(
                    'Test2ContentController',
                    'Test1ContentController',
                    'OxidEsales\EshopCommunity\Application\Controller\ContentController',
                    'oxUBase',
                    'OxidEsales\EshopCommunity\Application\Controller\FrontendController',
                    'oxView',
                    'OxidEsales\EshopCommunity\Core\Controller\BaseController',
                    'oxSuperCfg',
                    'OxidEsales\EshopCommunity\Core\Base'
                ),
                'expectedTitle'            => 'Impressum - Module_1_Controller - Module_3_Model - Module_2_Controller'
            )
        );
    }

    /**
     * Test, that the overwriting for the modules and their chain works.
     *
     * @dataProvider dataProviderForTestVirtualNamespaceModules
     */
    public function testVirtualNamespaceModules($modulesToActivate, $expectedInheritanceChain, $expectedTitle)
    {
        $this->environment->prepare($modulesToActivate);

        $createdContentController = oxNew('Content');

        $this->assertObjectHasInheritances(
            $createdContentController,
            $expectedInheritanceChain
        );

        $resultTitle = $createdContentController->getTitle();
        $this->assertSame($expectedTitle,$resultTitle);
    }

    /**
     * Assert, that the given object has the expected inheritance chain.
     *
     * @param object $objectUnderTest          The object, which should have the given inheritance chain.
     * @param array  $expectedInheritanceChain The inheritance chain we expect.
     */
    protected function assertObjectHasInheritances($objectUnderTest, $expectedInheritanceChain)
    {
        $classParents = array_keys(class_parents($objectUnderTest));
        $resultInheritanceChain = array_merge(array(get_class($objectUnderTest)), $classParents);

        $this->assertSame($expectedInheritanceChain, $resultInheritanceChain, 'The given object does not have the expected inheritance chain!');
    }
}
