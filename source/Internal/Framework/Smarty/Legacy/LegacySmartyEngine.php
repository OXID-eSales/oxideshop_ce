<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Smarty\Legacy;

use OxidEsales\EshopCommunity\Internal\Framework\Smarty\Bridge\SmartyEngineBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateEngineInterface;

/**
 * Class SmartyEngine.
 *
 * @internal
 */
class LegacySmartyEngine implements LegacySmartyEngineInterface, TemplateEngineInterface
{
    /**
     * The template engine.
     *
     * @var \Smarty
     */
    private $engine;

    /**
     * @var SmartyEngineBridgeInterface
     */
    private $bridge;

    /**
     * Array of global parameters.
     *
     * @var array
     */
    private $globals = [];

    /**
     * Constructor.
     */
    public function __construct(\Smarty $engine, SmartyEngineBridgeInterface $bridge)
    {
        $this->engine = $engine;
        $this->bridge = $bridge;
    }

    /**
     * Renders a template.
     *
     * @param string $name    A template name
     * @param array  $context An array of parameters to pass to the template
     *
     * @return string The evaluated template as a string
     */
    public function render(string $name, array $context = []): string
    {
        foreach ($context as $key => $value) {
            $this->engine->assign($key, $value);
        }
        if (isset($context['oxEngineTemplateId'])) {
            return $this->engine->fetch($name, $context['oxEngineTemplateId']);
        }

        return $this->engine->fetch($name);
    }

    /**
     * Renders a fragment of the template.
     *
     * @param string $fragment   The template fragment to render
     * @param string $fragmentId The Id of the fragment
     * @param array  $context    An array of parameters to pass to the template
     */
    public function renderFragment(string $fragment, string $fragmentId, array $context = []): string
    {
        return $this->bridge->renderFragment($this->engine, $fragment, $fragmentId, $context);
    }

    /**
     * Returns true if the template exists.
     *
     * @param string $name A template name
     *
     * @return bool true if the template exists, false otherwise
     */
    public function exists(string $name): bool
    {
        return $this->engine->template_exists($name);
    }

    /**
     * @param mixed $value
     */
    public function addGlobal(string $name, $value): void
    {
        $this->globals[$name] = $value;
        $this->engine->assign($name, $value);
    }

    /**
     * Returns assigned globals.
     */
    public function getGlobals(): array
    {
        return $this->globals;
    }

    /**
     * Returns the template file extension.
     */
    public function getDefaultFileExtension(): string
    {
        return 'tpl';
    }

    /**
     * Pass parameters to the Smarty instance.
     *
     * @param string $name  the name of the parameter
     * @param mixed  $value the value of the parameter
     */
    public function __set($name, $value): void
    {
        if (property_exists($this->engine, $name)) {
            $this->engine->$name = $value;
        }
    }

    /**
     * Pass parameters to the Smarty instance.
     *
     * @param string $name the name of the parameter
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->engine->$name;
    }

    public function getSmarty(): \Smarty
    {
        return $this->engine;
    }

    public function setSmarty(\Smarty $smarty): void
    {
        $this->engine = $smarty;
    }
}
