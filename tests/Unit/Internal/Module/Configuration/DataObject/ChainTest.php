<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Configuration\DataObject;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ClassExtensionsChain;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ChainTest extends TestCase
{
    public function testAddExtensionsIfChainIsEmpty()
    {
        $chain = new ClassExtensionsChain();
        $chain->addExtensions([
            'extendedClass'         => 'firstExtension',
            'anotherExtendedClass'  => 'someExtension',
        ]);

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
        $chain->addExtensions([
            'extendedClass'         => 'firstExtension',
            'anotherExtendedClass'  => 'someExtension',
        ]);

        $chain->addExtensions([
            'extendedClass' => 'secondExtension',
        ]);

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
        $chain->removeExtension('extendedClass1', 'extension1');
        $chain->removeExtension('extendedClass2', 'extension3');

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
     * @expectedException \OxidEsales\EshopCommunity\Internal\Module\Configuration\Exception\ExtensionNotInChainException
     *
     * @dataProvider invalidExtensionProvider
     */
    public function testRemoveExtensionThrowsExceptionIfClassNotExistsInChain($extended, $extension)
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
        $chain->removeExtension($extended, $extension);
    }

    public function invalidExtensionProvider()
    {
        return [
            ['notExistingExtended', 'notExistingExtension'],
            ['extendedClass1', 'notExistingExtension']
        ];
    }
}
