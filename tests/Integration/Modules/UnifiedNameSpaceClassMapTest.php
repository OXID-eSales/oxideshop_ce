<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Modules;

/**
 * Class UnifiedNameSpaceClassMapTest
 *
 * @group module
 * @package Integration\Modules
 */
class UnifiedNameSpaceClassMapTest extends BaseModuleTestCase
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

        $this->environment = new Environment();
    }

    /**
     * Standard tear down method. Calls parent last.
     */
    public function tearDown()
    {
        $this->environment->clean();

        parent::tearDown();
    }

    /**
     * Data provider for the method testUnifiedNamespaceModules.
     *
     * @return array The inputs for the method testUnifiedNamespaceModules.
     */
    public function dataProviderForTestUnifiedNamespaceModules()
    {
        return array(
            array(
                'modulesToActivate'          => array('unifiednamespace_module1'),
                'expectedInheritanceChain'   => array(
                    'Test1ContentController',
                    'OxidEsales\Eshop\Application\Controller\ContentController',
                    'OxidEsales\EshopCommunity\Application\Controller\ContentController',
                    'OxidEsales\Eshop\Application\Controller\FrontendController',
                    'OxidEsales\EshopCommunity\Application\Controller\FrontendController',
                    'OxidEsales\Eshop\Core\Controller\BaseController',
                    'OxidEsales\EshopCommunity\Core\Controller\BaseController',
                    'OxidEsales\Eshop\Core\Base',
                    'OxidEsales\EshopCommunity\Core\Base',
                ),
                'expectedInheritanceChainPE' => array(
                    'Test1ContentController',
                    'OxidEsales\Eshop\Application\Controller\ContentController',
                    'OxidEsales\EshopCommunity\Application\Controller\ContentController',
                    'OxidEsales\Eshop\Application\Controller\FrontendController',
                    'OxidEsales\EshopProfessional\Application\Controller\FrontendController',
                    'OxidEsales\EshopCommunity\Application\Controller\FrontendController',
                    'OxidEsales\Eshop\Core\Controller\BaseController',
                    'OxidEsales\EshopProfessional\Core\Controller\BaseController',
                    'OxidEsales\EshopCommunity\Core\Controller\BaseController',
                    'OxidEsales\Eshop\Core\Base',
                    'OxidEsales\EshopProfessional\Core\Base',
                    'OxidEsales\EshopCommunity\Core\Base',
                ),
                'expectedInheritanceChainEE' => array(
                    'Test1ContentController',
                    'OxidEsales\Eshop\Application\Controller\ContentController',
                    'OxidEsales\EshopCommunity\Application\Controller\ContentController',
                    'OxidEsales\Eshop\Application\Controller\FrontendController',
                    'OxidEsales\EshopEnterprise\Application\Controller\FrontendController',
                    'OxidEsales\EshopProfessional\Application\Controller\FrontendController',
                    'OxidEsales\EshopCommunity\Application\Controller\FrontendController',
                    'OxidEsales\Eshop\Core\Controller\BaseController',
                    'OxidEsales\EshopEnterprise\Core\Controller\BaseController',
                    'OxidEsales\EshopProfessional\Core\Controller\BaseController',
                    'OxidEsales\EshopCommunity\Core\Controller\BaseController',
                    'OxidEsales\Eshop\Core\Base',
                    'OxidEsales\EshopEnterprise\Core\Base',
                    'OxidEsales\EshopProfessional\Core\Base',
                    'OxidEsales\EshopCommunity\Core\Base'
                )
                ,
                'expectedTitle'              => 'Impressum - Module_1_Controller - Module_1_Model'
            ),
            array(
                'modulesToActivate'          => array('unifiednamespace_module1', 'unifiednamespace_module2'),
                'expectedInheritanceChain'   => array(
                    'Test2ContentController',
                    'Test1ContentController',
                    'OxidEsales\Eshop\Application\Controller\ContentController',
                    'OxidEsales\EshopCommunity\Application\Controller\ContentController',
                    'OxidEsales\Eshop\Application\Controller\FrontendController',
                    'OxidEsales\EshopCommunity\Application\Controller\FrontendController',
                    'OxidEsales\Eshop\Core\Controller\BaseController',
                    'OxidEsales\EshopCommunity\Core\Controller\BaseController',
                    'OxidEsales\Eshop\Core\Base',
                    'OxidEsales\EshopCommunity\Core\Base',
                ),
                'expectedInheritanceChainPE' => array(
                    'Test2ContentController',
                    'Test1ContentController',
                    'OxidEsales\Eshop\Application\Controller\ContentController',
                    'OxidEsales\EshopCommunity\Application\Controller\ContentController',
                    'OxidEsales\Eshop\Application\Controller\FrontendController',
                    'OxidEsales\EshopProfessional\Application\Controller\FrontendController',
                    'OxidEsales\EshopCommunity\Application\Controller\FrontendController',
                    'OxidEsales\Eshop\Core\Controller\BaseController',
                    'OxidEsales\EshopProfessional\Core\Controller\BaseController',
                    'OxidEsales\EshopCommunity\Core\Controller\BaseController',
                    'OxidEsales\Eshop\Core\Base',
                    'OxidEsales\EshopProfessional\Core\Base',
                    'OxidEsales\EshopCommunity\Core\Base',
                ),
                'expectedInheritanceChainEE' => array(
                    'Test2ContentController',
                    'Test1ContentController',
                    'OxidEsales\Eshop\Application\Controller\ContentController',
                    'OxidEsales\EshopCommunity\Application\Controller\ContentController',
                    'OxidEsales\Eshop\Application\Controller\FrontendController',
                    'OxidEsales\EshopEnterprise\Application\Controller\FrontendController',
                    'OxidEsales\EshopProfessional\Application\Controller\FrontendController',
                    'OxidEsales\EshopCommunity\Application\Controller\FrontendController',
                    'OxidEsales\Eshop\Core\Controller\BaseController',
                    'OxidEsales\EshopEnterprise\Core\Controller\BaseController',
                    'OxidEsales\EshopProfessional\Core\Controller\BaseController',
                    'OxidEsales\EshopCommunity\Core\Controller\BaseController',
                    'OxidEsales\Eshop\Core\Base',
                    'OxidEsales\EshopEnterprise\Core\Base',
                    'OxidEsales\EshopProfessional\Core\Base',
                    'OxidEsales\EshopCommunity\Core\Base'
                ),
                'expectedTitle'              => 'Impressum - Module_1_Controller - Module_1_Model - Module_2_Controller'
            ),
            array(
                'modulesToActivate'          => array('unifiednamespace_module1', 'unifiednamespace_module2', 'unifiednamespace_module3'),
                'expectedInheritanceChain'   => array(
                    'Test2ContentController',
                    'Test1ContentController',
                    'OxidEsales\Eshop\Application\Controller\ContentController',
                    'OxidEsales\EshopCommunity\Application\Controller\ContentController',
                    'OxidEsales\Eshop\Application\Controller\FrontendController',
                    'OxidEsales\EshopCommunity\Application\Controller\FrontendController',
                    'OxidEsales\Eshop\Core\Controller\BaseController',
                    'OxidEsales\EshopCommunity\Core\Controller\BaseController',
                    'OxidEsales\Eshop\Core\Base',
                    'OxidEsales\EshopCommunity\Core\Base',
                ),
                'expectedInheritanceChainPE' => array(
                    'Test2ContentController',
                    'Test1ContentController',
                    'OxidEsales\Eshop\Application\Controller\ContentController',
                    'OxidEsales\EshopCommunity\Application\Controller\ContentController',
                    'OxidEsales\Eshop\Application\Controller\FrontendController',
                    'OxidEsales\EshopProfessional\Application\Controller\FrontendController',
                    'OxidEsales\EshopCommunity\Application\Controller\FrontendController',
                    'OxidEsales\Eshop\Core\Controller\BaseController',
                    'OxidEsales\EshopProfessional\Core\Controller\BaseController',
                    'OxidEsales\EshopCommunity\Core\Controller\BaseController',
                    'OxidEsales\Eshop\Core\Base',
                    'OxidEsales\EshopProfessional\Core\Base',
                    'OxidEsales\EshopCommunity\Core\Base',
                ),
                'expectedInheritanceChainEE' => array(
                    'Test2ContentController',
                    'Test1ContentController',
                    'OxidEsales\Eshop\Application\Controller\ContentController',
                    'OxidEsales\EshopCommunity\Application\Controller\ContentController',
                    'OxidEsales\Eshop\Application\Controller\FrontendController',
                    'OxidEsales\EshopEnterprise\Application\Controller\FrontendController',
                    'OxidEsales\EshopProfessional\Application\Controller\FrontendController',
                    'OxidEsales\EshopCommunity\Application\Controller\FrontendController',
                    'OxidEsales\Eshop\Core\Controller\BaseController',
                    'OxidEsales\EshopEnterprise\Core\Controller\BaseController',
                    'OxidEsales\EshopProfessional\Core\Controller\BaseController',
                    'OxidEsales\EshopCommunity\Core\Controller\BaseController',
                    'OxidEsales\Eshop\Core\Base',
                    'OxidEsales\EshopEnterprise\Core\Base',
                    'OxidEsales\EshopProfessional\Core\Base',
                    'OxidEsales\EshopCommunity\Core\Base'
                ),
                'expectedTitle'              => 'Impressum - Module_1_Controller - Module_3_Model - Module_2_Controller'
            )
        );
    }

    /**
     * Test, that the overwriting for the modules and their chain works.
     *
     * @dataProvider dataProviderForTestUnifiedNamespaceModules
     */
    public function testUnifiedNamespaceModules($modulesToActivate, $expectedInheritanceChain, $expectedInheritanceChainPE, $expectedInheritanceChainEE, $expectedTitle)
    {
        foreach ($modulesToActivate as $moduleId) {
            $this->installAndActivateModule($moduleId);
        }

        $createdContentController = oxNew('Content');

        $expectedInheritanceChainEdition = $expectedInheritanceChain;

        if ($this->getTestConfig()->getShopEdition() == 'PE') {
            $expectedInheritanceChainEdition = $expectedInheritanceChainPE;
        }
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $expectedInheritanceChainEdition = $expectedInheritanceChainEE;
        }

        $this->assertObjectHasInheritances($createdContentController, $expectedInheritanceChainEdition);

        $resultTitle = $createdContentController->getTitle();
        $this->assertSame($expectedTitle, $resultTitle);
    }

    /**
     * Assert, that the given object has the expected inheritance chain.
     *
     * @param object $objectUnderTest          The object, which should have the given inheritance chain.
     * @param array  $expectedInheritanceChain The inheritance chain we expect.
     */
    private function assertObjectHasInheritances($objectUnderTest, $expectedInheritanceChain)
    {
        $classParents = array_keys(class_parents($objectUnderTest));
        $resultInheritanceChain = array_merge(array(get_class($objectUnderTest)), $classParents);

        $this->assertSame($expectedInheritanceChain, $resultInheritanceChain, 'The given object does not have the expected inheritance chain!');
    }
}
