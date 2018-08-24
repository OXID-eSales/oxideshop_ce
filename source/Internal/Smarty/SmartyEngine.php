<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Smarty;

use OxidEsales\EshopCommunity\Internal\Templating\BaseEngineInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;

/**
 * Class SmartyEngine
 *
 * @package OxidEsales\EshopCommunity\Internal\Smarty
 */
class SmartyEngine implements BaseEngineInterface
{
    /**
     * @var string
     */
    private $cacheId;

    /**
     * The template engine.
     *
     * @var \Smarty
     */
    private $engine;

    /**
     * @var TemplateNameParserInterface
     */
    protected $parser;

    /**
     * Array of global parameters
     *
     * @var array
     */
    private $globals = [];

    /**
     * Constructor.
     *
     * @param \Smarty                     $engine
     * @param TemplateNameParserInterface $parser
     */
    public function __construct(\Smarty $engine, TemplateNameParserInterface $parser)
    {
        $this->engine = $engine;
        $this->parser = $parser;
    }

    /**
     * Render the template.
     *
     * @param string $name       The name of the template
     * @param array  $parameters Parameters to assign
     *
     * @return string
     */
    public function render($name, array $parameters = array())
    {
        // attach the global variables
       // $parameters = array_replace($this->getGlobals(), $parameters);
        foreach ($parameters as $key => $value) {
            $this->engine->assign($key, $value);
        }
        if (isset($this->cacheId)) {
            return $this->engine->fetch($name, $this->cacheId);
        }
        return $this->engine->fetch($name);
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function addGlobal($name, $value)
    {
        $this->globals[$name] = $value;
        $this->engine->assign($name, $value);
    }

    /**
     * Returns the assigned globals.
     *
     * @return array
     */
    public function getGlobals()
    {
        return $this->globals;
    }

    /**
     * Checks whether the specified template exists.
     * It can accept either a path to the template on the filesystem or a resource string specifying the template.
     *
     * @param string $name A template name
     *
     * @return bool True if the template exists, false otherwise
     */
    public function exists($name)
    {
        return $this->engine->template_exists($name);
    }

    /**
     * @param string $cacheId
     */
    public function setCacheId($cacheId)
    {
        $this->cacheId = $cacheId;
    }

    /**
     * Returns true if this class is able to render the given template.
     *
     * @param string $name A template name
     *
     * @return Boolean True if this class supports the given resource, false otherwise
     */
    public function supports($name)
    {
        $template = $this->parser->parse($name);

        return in_array($template->get('engine'), array('smarty', 'tpl'), true);
    }

    /**
     * Pass parameters to the Smarty instance.
     *
     * @param string $name  The name of the parameter.
     * @param mixed  $value The value of the parameter.
     */
    public function __set($name, $value)
    {
        $this->engine->$name = $value;
    }

    /**
     * Pass parameters to the Smarty instance.
     *
     * @param string $name The name of the parameter.
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->engine->$name;
    }
}
