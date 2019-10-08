<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ExtensionNotInChainException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\ClassExtension;

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
     * @param ClassExtension[] $extensions
     *
     * @return void
     */
    public function addExtensions(array $extensions) : void
    {
        foreach ($extensions as $extension) {
            $this->addExtension($extension);
        }
    }

    /**
     * @param ClassExtension $classExtension
     *
     * @throws ExtensionNotInChainException
     */
    public function removeExtension(ClassExtension $classExtension): void
    {
        $extended = $classExtension->getShopClassName();
        $extension = $classExtension->getModuleExtensionClassName();
        
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
     * @param ClassExtension $extension
     */
    public function addExtension(ClassExtension $extension): void
    {
        if (array_key_exists($extension->getShopClassName(), $this->chain)) {
            if (!$this->isModuleExtensionClassNameInChain($extension)) {
                array_push(
                    $this->chain[$extension->getShopClassName()],
                    $extension->getModuleExtensionClassName()
                );
            }
        } else {
            $this->chain[$extension->getShopClassName()] = [$extension->getModuleExtensionClassName()];
        }
    }

    /**
     * @param ClassExtension $extension
     *
     * @return bool
     */
    private function isModuleExtensionClassNameInChain(ClassExtension $extension): bool
    {
        if (in_array($extension->getModuleExtensionClassName(), $this->chain[$extension->getShopClassName()])) {
            return true;
        }

        return false;
    }

    /**
     * @return \Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->chain);
    }
}
