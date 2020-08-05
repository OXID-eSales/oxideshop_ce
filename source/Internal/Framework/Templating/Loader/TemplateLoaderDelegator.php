<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating\Loader;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

class TemplateLoaderDelegator implements TemplateLoaderInterface
{
    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * @var TemplateLoaderInterface
     */
    private $adminLoader;

    /**
     * @var TemplateLoaderInterface
     */
    private $frontendLoader;

    /**
     * TemplateLoaderDelegator constructor.
     * @param ContextInterface $context
     * @param TemplateLoaderInterface $adminLoader
     * @param TemplateLoaderInterface $frontendLoader
     */
    public function __construct(
        ContextInterface $context,
        TemplateLoaderInterface $adminLoader,
        TemplateLoaderInterface $frontendLoader
    ) {
        $this->context = $context;
        $this->adminLoader = $adminLoader;
        $this->frontendLoader = $frontendLoader;
    }

    public function exists($name): bool
    {
        return $this->getLoader()->exists($name);
    }

    public function getContext($name): string
    {
        return $this->getLoader()->getContext($name);
    }

    private function getLoader(): TemplateLoaderInterface
    {
        return $this->context->isAdmin()
            ? $this->adminLoader
            : $this->frontendLoader;
    }
}
