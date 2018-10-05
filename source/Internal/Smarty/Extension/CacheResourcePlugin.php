<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Smarty\Extension;

use OxidEsales\EshopCommunity\Internal\Smarty\SmartyContextInterface;

/**
 * Cache resource
 */
class CacheResourcePlugin
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
     * Sets template content from cache. In demoshop enables security mode.
     *
     * @see http://www.smarty.net/docsv2/en/template.resources.tpl
     *
     * @param string $templateName   The name of template
     * @param string $templateSource The template source
     * @param object $smarty         The smarty object
     *
     * @return bool
     */
    public static function getTemplate($templateName, &$templateSource, $smarty)
    {
        if (isset($smarty->oxidcache) && isset($smarty->oxidcache->value)) {
            $templateSource = $smarty->oxidcache->value;
        }
        if (self::$context->getTemplateSecurityMode()) {
            $smarty->security = true;
        }

        return true;
    }

    /**
     * Sets time for smarty templates recompilation. If oxidtimecache is set, smarty will cache templates for this period.
     * Otherwise templates will always be compiled.
     *
     * @see http://www.smarty.net/docsv2/en/template.resources.tpl
     *
     * @param string $templateName      The name of template
     * @param string $templateTimestamp The template timestamp reference
     * @param object $smarty            The smarty object
     *
     * @return bool
     */
    public static function getTimestamp($templateName, &$templateTimestamp, $smarty)
    {
        $templateTimestamp = isset($smarty->oxidtimecache->value) ? $smarty->oxidtimecache->value : time();

        return true;
    }

    /**
     * Dummy function, required for smarty plugin registration.
     *
     * @see http://www.smarty.net/docsv2/en/template.resources.tpl
     *
     * @param string $templateName The name of template
     * @param object $smarty       The smarty object
     *
     * @return bool
     */
    public static function getSecure($templateName, $smarty)
    {
        return true;
    }

    /**
     * Dummy function, required for smarty plugin registration.
     *
     * @see http://www.smarty.net/docsv2/en/template.resources.tpl
     *
     * @param string $templateName The name of template
     * @param object $smarty       The smarty object
     */
    public static function getTrusted($templateName, $smarty)
    {
    }
}
