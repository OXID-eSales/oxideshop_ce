<?php
/**
 * Created by PhpStorm.
 * User: vilma
 * Date: 02.08.18
 * Time: 09:34
 */

namespace OxidEsales\EshopCommunity\Core;

use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;

class SmartyEngine implements EngineInterface
{
    private $smarty;

    private $parser;

    private $cacheId;
    /**
     * Constructor.
     *
     * @param \Smarty                     $environment A Smarty instance
     * @param TemplateNameParserInterface $parser      A TemplateNameParserInterface instance
     */
    public function __construct(\Smarty $smarty, TemplateNameParserInterface $parser)
    {
        // The parser to use.
        $this->parser = $parser;
        // Create the Smarty template engine.
        $this->smarty = $smarty;
        // Set any of the required configuration.
    }
    /**
     * Renders a template.
     *
     * @param string|TemplateReferenceInterface $name       A template name or a TemplateReferenceInterface instance
     * @param array                             $parameters An array of parameters to pass to the template
     *
     * @return string The evaluated template as a string
     *
     * @throws \RuntimeException if the template cannot be rendered
     *
     * @api
     */
    public function render($name, array $parameters = array())
    {
        foreach (array_keys($parameters) as $viewName) {
            $this->smarty->assign_by_ref($viewName, $parameters[$viewName]);
        }
        if (isset($this->cacheId)) {
            return $this->smarty->fetch($name, $this->cacheId);
        }
        // Render the template using Smarty.
        return $this->smarty->fetch($name);
    }
    /**
     * Returns true if the template exists.
     *
     * @param string|TemplateReferenceInterface $name A template name or a TemplateReferenceInterface instance
     *
     * @return bool    true if the template exists, false otherwise
     *
     * @throws \RuntimeException if the engine cannot handle the template name
     *
     * @api
     */
    public function exists($name)
    {
        return $this->smarty->templateExists($this->nameToString($name));
    }
    /**
     * Returns true if this class is able to render the given template.
     *
     * @param string|TemplateReferenceInterface $name A template name or a TemplateReferenceInterface instance
     *
     * @return bool    true if this class supports the given template, false otherwise
     *
     * @api
     */
    public function supports($name)
    {
        $template = $this->parser->parse($name);
        return 'smarty' === $template->get('engine');
    }

    public function setCacheId($cacheId)
    {
        $this->cacheId = $cacheId;
    }

    /**
     * Converts a template name to string if it is anything other than a string.
     */
    private function nameToString($name)
    {
        return is_string($name) ? $name : $name->toString();
    }
}