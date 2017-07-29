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
namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Module;

use OxidEsales\Eshop\Core\Registry;

/**
 * @group module
 * @package Unit\Core
 */
class ModuleMetadataValidatorTest extends \OxidTestCase
{

    public function testValidateModuleWithoutMetadataFile()
    {
        $PathToMetadata = '';
        $moduleStub = $this->getMock(\OxidEsales\Eshop\Core\Module\Module::class, array('getMetadataPath'));
        $moduleStub->expects($this->any())
            ->method('getMetadataPath')
            ->will($this->returnValue($PathToMetadata));

        /** @var \OxidEsales\Eshop\Core\Module\Module $moduleStub */
        $module = $moduleStub;

        $metadataValidator = oxNew('oxModuleMetadataValidator');
        $this->assertFalse($metadataValidator->validate($module));
    }

    public function testValidateModuleWithValidMetadataFile()
    {
        $metadataFileName = 'metadata.php';
        $metadataContent = '<?php ';

        $pathToMetadata = $this->createFile($metadataFileName, $metadataContent);

        $moduleStub = $this->getMock(\OxidEsales\Eshop\Core\Module\Module::class, array('getMetadataPath'));
        $moduleStub->expects($this->any())
            ->method('getMetadataPath')
            ->will($this->returnValue($pathToMetadata));

        $module = $moduleStub;

        $oMetadataValidator = oxNew('oxModuleMetadataValidator');
        $this->assertTrue($oMetadataValidator->validate($module));
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function dataProviderTestValidateExtendSection()
    {
        $data = [
            'all_is_well' => ['metadata_extend' =>
                                        [\OxidEsales\Eshop\Application\Model\Article::class => '\MyVendor\MyModule1\MyArticleClass',
                                         \OxidEsales\Eshop\Application\Model\Order::class => '\MyVendor\MyModule1\MyOrderClass',
                                         \OxidEsales\Eshop\Application\Model\User::class => '\MyVendor\MyModule1\MyUserClass'
                                        ],
                                    'expected' => []
            ],
            'all_is_well_bc' => ['metadata_extend' =>
                                           ['oxArticle' => '\MyVendor\MyModule1\MyArticleClass',
                                            'oxOrder' => '\MyVendor\MyModule1\MyOrderClass',
                                            'oxUser' => '\MyVendor\MyModule1\MyUserClass'
                                            ],
                                       'expected' => []
            ],
            'all_is_well_extend_non_shop_namespace' => ['metadata_extend' =>
                                     ['\SomeVendor\SomeNamespace\Article' => '\MyVendor\MyModule1\MyArticleClass',
                                      '\somevendor\SomeOtherNamespace\Order' => '\MyVendor\MyModule1\MyOrderClass',
                                      'oxUser' => '\MyVendor\MyModule1\MyUserClass'
                                     ],
                                 'expected' => []
            ],
            'all_is_well_extend_shop_edition_test_namespace' => ['metadata_extend' =>
                                                            ['\OxidEsales\EshopCommunity\Tests\SomeVendor\SomeNamespace\Article' => '\MyVendor\MyModule1\MyArticleClass',
                                                             '\somevendor\SomeOtherNamespace\Order' => '\MyVendor\MyModule1\MyOrderClass',
                                                             'oxUser' => '\MyVendor\MyModule1\MyUserClass'
                                                            ],
                                                        'expected' => ['\OxidEsales\EshopCommunity\Tests\SomeVendor\SomeNamespace\Article' => '\MyVendor\MyModule1\MyArticleClass']
            ],
            'edition_instead_of_vns' => ['metadata_extend' =>
                                                   [\OxidEsales\Eshop\Application\Model\Article::class => '\MyVendor\MyModule1\MyArticleClass',
                                                    \OxidEsales\EshopCommunity\Application\Model\Order::class => '\MyVendor\MyModule1\MyOrderClass',
                                                    \OxidEsales\EshopCommunity\Application\Model\User::class => '\MyVendor\MyModule1\MyUserClass'
                                                    ],
                                               'expected' => [\OxidEsales\EshopCommunity\Application\Model\Order::class => '\MyVendor\MyModule1\MyOrderClass',
                                                              \OxidEsales\EshopCommunity\Application\Model\User::class => '\MyVendor\MyModule1\MyUserClass']
            ]
        ];

        return $data;
    }

    /**
     * Test metadata extend section validation.
     *
     * @param array $metadataExtend
     * @param array $expected
     *
     * @dataProvider dataProviderTestValidateExtendSection
     */
    public function testGetIncorrectExtensions($metadataExtend, $expected)
    {
        $moduleMock = $this->getMock(\OxidEsales\Eshop\Core\Module\Module::class, array('getId', 'getExtensions'));
        $moduleMock->expects($this->once())->method('getExtensions')->will($this->returnValue($metadataExtend));
        $validator = oxNew(\OxidEsales\EshopCommunity\Core\Module\ModuleMetadataValidator::class);

        $this->assertEquals($expected, $validator->getIncorrectExtensions($moduleMock));
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function dataProviderCheckModuleExtensionsForIncorrectNamespaceClasses()
    {
        $data = [
            'edition_instead_of_vns' => ['metadata_extend' =>
                                             [\OxidEsales\Eshop\Application\Model\Article::class => '\MyVendor\MyModule1\MyArticleClass',
                                              \OxidEsales\EshopCommunity\Application\Model\Order::class => '\MyVendor\MyModule1\MyOrderClass',
                                              \OxidEsales\EshopCommunity\Application\Model\User::class => '\MyVendor\MyModule1\MyUserClass'
                                             ],
                                         'expected' => 'OxidEsales\EshopCommunity\Application\Model\Order => \MyVendor\MyModule1\MyOrderClass, ' .
                                                       'OxidEsales\EshopCommunity\Application\Model\User => \MyVendor\MyModule1\MyUserClass'
            ]
        ];

        return $data;
    }

    /**
     * Test metadata extend section validation.
     *
     * @param array $metadata
     * @param array $expected
     *
     * @dataProvider dataProviderCheckModuleExtensionsForIncorrectNamespaceClasses
     */
    public function testCheckModuleExtensionsForIncorrectNamespaceClasses($metadataExtend, $expected)
    {
        $moduleMock = $this->getMock(\OxidEsales\Eshop\Core\Module\Module::class, array('getId', 'getExtensions'));
        $moduleMock->expects($this->once())->method('getExtensions')->will($this->returnValue($metadataExtend));
        $validator = oxNew(\OxidEsales\EshopCommunity\Core\Module\ModuleMetadataValidator::class);

        $message = sprintf(Registry::getLang()->translateString('MODULE_METADATA_PROBLEMATIC_DATA_IN_EXTEND', null, true), $expected);
        $this->setExpectedException(\OxidEsales\Eshop\Core\Exception\ModuleValidationException::class, $message);

        $validator->checkModuleExtensionsForIncorrectNamespaceClasses($moduleMock);
    }
}
