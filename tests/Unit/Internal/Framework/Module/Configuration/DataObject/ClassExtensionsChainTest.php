<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Configuration\DataObject;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ClassExtensionsChain;
use PHPUnit\Framework\TestCase;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\ClassExtension;

/**
 * @internal
 */
class ClassExtensionsChainTest extends TestCase
{
    public function testAddExtensionsIfChainIsEmpty()
    {
        $chain = new ClassExtensionsChain();

        $chain->addExtensions(
            [
                new ClassExtension(
                    'extendedClass',
                    'firstExtension'
                ),
                new ClassExtension(
                    'anotherExtendedClass',
                    'someExtension'
                )
            ]
        );

        $this->assertEquals(
            [
                'extendedClass' => [
                    'firstExtension',
                ],
                'anotherExtendedClass' => [
                    'someExtension',
                ],
            ],
            $chain->getChain()
        );
    }

    public function testAddExtensionToChainIfAnotherExtensionsAlreadyExist()
    {
        $chain = new ClassExtensionsChain();

        $chain->addExtensions(
            [
                new ClassExtension(
                    'extendedClass',
                    'firstExtension'
                ),
                new ClassExtension(
                    'anotherExtendedClass',
                    'someExtension'
                ),
                new ClassExtension(
                    'extendedClass',
                    'secondExtension'
                ),
                new ClassExtension(
                    'extendedClass',
                    'firstExtension'
                )
            ]
        );

        $chain->addExtension(
            new ClassExtension(
                'extendedClass',
                'firstExtension'
            )
        );

        $this->assertEquals(
            [
                'extendedClass' => [
                    'firstExtension',
                    'secondExtension',
                ],
                'anotherExtendedClass' => [
                    'someExtension',
                ]
            ],
            $chain->getChain()
        );
    }

    public function testRemoveExtension()
    {
        $chain = new ClassExtensionsChain();
        $chain->setChain(
            [
                'extendedClass1' => [
                    'extension1',
                    'extension2',
                ],
                'extendedClass2' => [
                    'extension3',
                ],
                'extendedClass3' => [
                    'extension4'
                ]
            ]
        );
        $chain->removeExtension(
            new ClassExtension(
                'extendedClass1',
                'extension1'
            )
        );
        $chain->removeExtension(
            new ClassExtension(
                'extendedClass2',
                'extension3'
            )
        );

        $this->assertEquals(
            [
                'extendedClass1' => [
                    'extension2',
                ],
                'extendedClass3' => [
                    'extension4'
                ]
            ],
            $chain->getChain()
        );
    }

    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ExtensionNotInChainException
     *
     * @dataProvider invalidExtensionProvider
     *
     * @param ClassExtension $extension
     *
     */
    public function testRemoveExtensionThrowsExceptionIfClassNotExistsInChain(ClassExtension $extension)
    {
        $chain = new ClassExtensionsChain();
        $chain->setChain(
            [
                'extendedClass1' => [
                    'extension1',
                    'extension2',
                ]
            ]
        );
        $chain->removeExtension($extension);
    }

    public function invalidExtensionProvider()
    {
        return [
            [
                new ClassExtension(
                    'notExistingExtended',
                    'notExistingExtension'
                )
            ],
            [
                new ClassExtension(
                    'extendedClass1',
                    'notExistingExtension'
                )
            ],
        ];
    }
}
