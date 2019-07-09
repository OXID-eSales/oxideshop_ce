<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\Exception\ExtensionNotInChainException;

/**
 * @internal
 */
class ClassExtensionsChain implements \IteratorAggregate
{
    const NAME = 'classExtensions';

    /**
     * @var array
     */
    private $chain = [];

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @return array
     */
    public function getChain(): array
    {
        return $this->chain;
    }

    /**
     * @param array $chain
     * @return ClassExtensionsChain
     */
    public function setChain(array $chain): ClassExtensionsChain
    {
        $this->chain = $chain;
        return $this;
    }

    /**
     * @param array $extensions
     */
    public function addExtensions(array $extensions)
    {
        foreach ($extensions as $extended => $extension) {
            $this->addExtensionToChain($extended, $extension);
        }
    }

    /**
     * @param string $extended
     * @param string $extension
     *
     * @throws ExtensionNotInChainException
     */
    public function removeExtension(string $extended, string $extension)
    {
        if (false === array_key_exists($extended, $this->chain) ||
            false === \array_search($extension, $this->chain[$extended], true)) {
            throw new ExtensionNotInChainException(
                'There is no class ' . $extended . ' extended by class ' .
                $extension . ' in the current chain'
            );
        }

        $resultOffset = \array_search($extension, $this->chain[$extended], true);
        unset($this->chain[$extended][$resultOffset]);
        $this->chain[$extended] = \array_values($this->chain[$extended]);

        if (empty($this->chain[$extended])) {
            unset($this->chain[$extended]);
        }
    }

    /**
     * @param string $extended
     * @param string $extension
     */
    private function addExtensionToChain(string $extended, string $extension)
    {
        if (array_key_exists($extended, $this->chain)) {
            array_push($this->chain[$extended], $extension);
        } else {
            $this->chain[$extended] = [$extension];
        }
    }

    /**
     * @return \Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->chain);
    }
}
