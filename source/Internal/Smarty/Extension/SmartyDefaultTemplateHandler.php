<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Smarty\Extension;

use OxidEsales\EshopCommunity\Internal\Smarty\SmartyContextInterface;

/**
 * Default Template Handler
 *
 * called when Smarty's file: resource is unable to load a requested file
 */
class SmartyDefaultTemplateHandler
{
    /**
     * @var SmartyContextInterface
     */
    private static $context;

    /**
     * @param SmartyContextInterface $context
     */
    public function __construct(SmartyContextInterface $context)
    {
        self::$context = $context;
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
        $config = self::$context;
        if ($resourceType == 'file' && !is_readable($resourceName)) {
            $resourceName = $config->getTemplatePath($resourceName);
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
