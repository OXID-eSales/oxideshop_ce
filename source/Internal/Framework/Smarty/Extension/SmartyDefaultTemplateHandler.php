<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Smarty\Extension;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Loader\TemplateLoaderInterface;

/**
 * Default Template Handler.
 *
 * called when Smarty's file: resource is unable to load a requested file
 */
class SmartyDefaultTemplateHandler
{
    /**
     * @var TemplateLoaderInterface
     */
    private static $loader;

    public function __construct(TemplateLoaderInterface $loader)
    {
        self::$loader = $loader;
    }

    /**
     * Called when a template cannot be obtained from its resource.
     *
     * @param string $resourceType      template type
     * @param string $resourceName      template file name
     * @param string $resourceContent   template file content
     * @param int    $resourceTimestamp template file timestamp
     * @param object $smarty            template processor object (smarty)
     *
     * @return bool
     */
    public function handleTemplate($resourceType, $resourceName, &$resourceContent, &$resourceTimestamp, $smarty)
    {
        $loader = self::$loader;
        if ('file' === $resourceType && !is_readable($resourceName)) {
            $resourceName = $loader->getPath($resourceName);
            $fileLoaded = is_file($resourceName) && is_readable($resourceName);
            if ($fileLoaded) {
                $resourceContent = $smarty->_read_file($resourceName);
                $resourceTimestamp = filemtime($resourceName);
            }

            return $fileLoaded;
        }

        return false;
    }
}
