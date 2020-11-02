<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating\Locator;

use OxidEsales\Eshop\Core\Config;

/**
 * Class AdminTemplateFileLocator.
 */
class AdminTemplateFileLocator implements FileLocatorInterface
{
    /**
     * @var Config
     */
    private $context;

    /**
     * FileLocator constructor.
     */
    public function __construct(Config $context)
    {
        $this->context = $context;
    }

    /**
     * Returns a full path for a given file name.
     *
     * @param string $name The file name to locate
     *
     * @return string The full path to the file
     */
    public function locate($name): string
    {
        return $this->context->getTemplatePath($name, true);
    }
}
