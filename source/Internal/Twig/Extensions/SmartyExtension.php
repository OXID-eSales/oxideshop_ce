<?php

namespace OxidEsales\EshopCommunity\Internal\Twig\Extensions;

use Twig\Error\Error;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class SmartyExtension
 */
class SmartyExtension extends AbstractExtension
{

    private $cycleVars;

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('smarty_cycle', [$this, 'smartyCycle'])
        ];
    }

    /**
     * @param array $values
     * @param array $parameters
     *
     * @return mixed|null
     */
    public function smartyCycle(array $values = [], array $parameters = [])
    {
        $name = (empty($parameters['name'])) ? 'default' : $parameters['name'];
        $advance = (isset($parameters['advance'])) ? (bool) $parameters['advance'] : true;
        $reset = (isset($parameters['reset'])) ? (bool) $parameters['reset'] : false;
        $return = (isset($parameters['print'])) ? (bool) $parameters['print'] : true;

        if (empty($values)) {
            if (!isset($this->cycleVars[$name]['values'])) {
                throw new Error("static_cycle: missing 'values' parameter");
            }
        } else {
            if (isset($this->cycleVars[$name]['values'])
                && $this->cycleVars[$name]['values'] != $values) {
                $this->cycleVars[$name]['index'] = 0;
            }
            $this->cycleVars[$name]['values'] = $values;
        }

        if (isset($parameters['delimiter'])) {
            $this->cycleVars[$name]['delimiter'] = $parameters['delimiter'];
        } elseif (!isset($this->cycleVars[$name]['delimiter'])) {
            $this->cycleVars[$name]['delimiter'] = ',';
        }

        if (is_array($this->cycleVars[$name]['values'])) {
            $cycleArray = $this->cycleVars[$name]['values'];
        } else {
            $cycleArray = explode($this->cycleVars[$name]['delimiter'], $this->cycleVars[$name]['values']);
        }

        if (!isset($this->cycleVars[$name]['index']) || $reset) {
            $this->cycleVars[$name]['index'] = 0;
        }

        $value = $cycleArray[$this->cycleVars[$name]['index']];

        if ($advance) {
            if ($this->cycleVars[$name]['index'] >= count($cycleArray) - 1) {
                $this->cycleVars[$name]['index'] = 0;
            } else {
                $this->cycleVars[$name]['index']++;
            }
        }

        return $return ? $value : null;
    }
}
