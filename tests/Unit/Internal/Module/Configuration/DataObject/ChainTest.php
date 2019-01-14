<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Configuration\DataObject;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\Chain;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ChainTest extends TestCase
{
    public function testAddExtensionsIfChainIsEmpty()
    {
        $chain = new Chain();
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
        $chain = new Chain();
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
}
