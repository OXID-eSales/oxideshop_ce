<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating\Resolver;

/**
 * Class LegacyTemplateNameResolver.
 */
class LegacyTemplateNameResolver implements TemplateNameResolverInterface
{
    /**
     * @var TemplateNameResolverInterface
     */
    private $resolver;

    /**
     * TemplateNameResolver constructor.
     */
    public function __construct(TemplateNameResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    public function resolve(string $name): string
    {
        return $this->resolver->resolve($this->getFileNameWithoutExtension($name));
    }

    private function getFileNameWithoutExtension(string $fileName): string
    {
        $pos = strrpos($fileName, '.tpl');
        if (false !== $pos) {
            $fileName = substr($fileName, 0, $pos);
        }

        return $fileName;
    }
}
