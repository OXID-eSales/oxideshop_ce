<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\ShopEvents;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class OnExecuteEvent
 *
 * @package OxidEsales\EshopCommunity\Internal\ShopEvents
 */
class OnExecuteEvent extends Event
{
    const NAME = 'oxidesales.onexecute';

    /**
     * @var string
     */
    protected $class = null;

    /**
     * @var string
     */
    protected $method = null;

    /**
     * Method arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * Setter for class which triggers this event.
     *
     * @param string $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * Getter for class which triggers this event.
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Setter for method which triggers this event.
     *
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * Getter for method which triggers this event.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Setter for method arguments
     *
     * @param array $arguments
     */
    public function setArguments($arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * Getter for method arguments
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }
}
