<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Configuration\DataObject;

use PHPUnit\Framework\Attributes\DataProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ClassExtensionsChain;
use PHPUnit\Framework\TestCase;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\ClassExtension;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ExtensionNotInChainException;

/**
 * @internal
 */
final class ClassExtensionsChainTest extends TestCase
{
    public function testAddExtensionsIfChainIsEmpty(): void
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

    public function testAddExtensionToChainIfAnotherExtensionsAlreadyExist(): void
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

    public function testRemoveExtension(): void
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


    #[DataProvider('invalidExtensionProvider')]
    public function testRemoveExtensionThrowsExceptionIfClassNotExistsInChain(ClassExtension $extension): void
    {
        $this->expectException(ExtensionNotInChainException::class);
        $chain = new ClassExtensionsChain();
        $chain->setChain(
            [
                'extendedClass1' => [
                    'extension1',
                    'extension2',
                ]
            ]
        );
        $this->expectException(
            ExtensionNotInChainException::class
        );
        $chain->removeExtension($extension);
    }

    public static function invalidExtensionProvider(): array
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
