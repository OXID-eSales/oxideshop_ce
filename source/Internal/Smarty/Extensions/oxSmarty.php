<?php
/**
 * Created by PhpStorm.
 * User: vilma
 * Date: 03.08.18
 * Time: 11:53
 */

namespace OxidEsales\EshopCommunity\Internal\Smarty\Extensions;

use OxidEsales\EshopCommunity\Internal\Smarty\SmartyContextInterface;

/**
 * Class oxSmarty
 * @package OxidEsales\EshopCommunity\Internal\Smarty\Extensions
 */
class oxSmarty
{
    /**
     * @var SmartyContextInterface
     */
    private static $context;

    /**
     * @var oxSmarty
     */
    private static $instance;

    /**
     * oxSmarty constructor.
     *
     * @param SmartyContextInterface $context
     */
    private function __construct(SmartyContextInterface $context)
    {
        self::$context = $context;
    }

    /**
     * @param SmartyContextInterface $context
     *
     * @return oxSmarty
     */
    public static function getInstance(SmartyContextInterface $context)
    {
        if (self::$instance === null) {
            self::$instance = new oxSmarty($context);
        }
        return self::$instance;
    }

    /**
     * Sets template content from cache. In demoshop enables security mode.
     *
     * @see http://www.smarty.net/docsv2/en/template.resources.tpl
     *
     * @param string $sTplName   The name of template
     * @param string $sTplSource Template source
     * @param object $oSmarty    Smarty object
     *
     * @return bool
     */
    public static function ox_get_template($sTplName, &$sTplSource, $oSmarty)
    {
        $sTplSource = $oSmarty->oxidcache->value;
        if (self::$context->getTemplateSecurityMode()) {
            $oSmarty->security = true;
        }

        return true;
    }

    /**
     * Sets time for smarty templates recompilation. If oxidtimecache is set, smarty will cache templates for this period.
     * Otherwise templates will always be compiled.
     *
     * @see http://www.smarty.net/docsv2/en/template.resources.tpl
     *
     * @param string $sTplName      name of template
     * @param string $iTplTimestamp template timestamp referense
     * @param object $oSmarty       not used here
     *
     * @return bool
     */
    public static function ox_get_timestamp($sTplName, &$iTplTimestamp, $oSmarty)
    {
        $iTplTimestamp = isset($oSmarty->oxidtimecache->value) ? $oSmarty->oxidtimecache->value : time();

        return true;
    }

    /**
     * Dummy function, required for smarty plugin registration.
     *
     * @see http://www.smarty.net/docsv2/en/template.resources.tpl
     *
     * @param string $sTplName not used here
     * @param object $oSmarty  not used here
     *
     * @return bool
     */
    public static function ox_get_secure($sTplName, $oSmarty)
    {
        return true;
    }

    /**
     * Dummy function, required for smarty plugin registration.
     *
     * @see http://www.smarty.net/docsv2/en/template.resources.tpl
     *
     * @param string $sTplName not used here
     * @param object $oSmarty  not used here
     */
    public static function ox_get_trusted($sTplName, $oSmarty)
    {
    }

    /**
     * is called when a template cannot be obtained from its resource.
     *
     * @param string $resourceType      template type
     * @param string $resourceName      template file name
     * @param string $resourceContent   template file content
     * @param int    $resourceTimestamp template file timestamp
     * @param object $smarty            template processor object (smarty)
     *
     * @return bool
     */
    public function _smartyDefaultTemplateHandler($resourceType, $resourceName, &$resourceContent, &$resourceTimestamp, $smarty)
    {
        $config = self::$context;
        if ($resourceType == 'file' && !is_readable($resourceName)) {
            $resourceName = $config->getTemplatePath($resourceName);
            $resourceContent = $smarty->_read_file($resourceName);
            $resourceTimestamp = filemtime($resourceName);

            return is_file($resourceName) && is_readable($resourceName);
        }

        return false;
    }
}