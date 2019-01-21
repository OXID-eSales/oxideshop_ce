<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\ShopEvents;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class IncludeWidgetEvent
 *
 * @package OxidEsales\EshopCommunity\Internal\ShopEvents
 */
class IncludeWidgetEvent extends Event
{
    const NAME = self::class;

    /**
     * Result
     *
     * @var string
     */
    private $result = '';

    /**
     * @var Smarty
     */
    private $smarty;

    /**
     * @var array
     */
    private $parameters;

    /**
     * IncludeWidgetEvent constructor.
     *
     * @param \Smarty $smarty     Smarty object
     * @param array   $parameters Parameters needed for smarty
     */
    public function __construct(\Smarty $smarty, array $parameters)
    {
        $this->smarty = $smarty;
        $this->parameters = $parameters;
    }

    /**
     * Setter for result.
     *
     * @param string $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }

    /**
     * Getter for parameters
     *
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Getter for smarty.
     *
     * @return \Smarty
     */
    public function getSmarty(): \Smarty
    {
        return $this->smarty;
    }

    /**
     * Getter for result
     *
     * @return string
     */
    public function getResult(): string
    {
        return $this->result;
    }
}
