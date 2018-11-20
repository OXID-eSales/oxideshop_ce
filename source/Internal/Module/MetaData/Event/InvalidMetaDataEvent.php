<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\MetaData\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class InvalidMetaDataEvent
 *
 * @package OxidEsales\EshopCommunity\Internal\Module\MetaData\Event
 */
class InvalidMetaDataEvent extends Event
{
    /**
     * Name of the event
     */
    const NAME = self::class;

    /**
     * @var string
     */
    private $level;

    /**
     * @var string
     */
    private $message;

    /**
     * InvalidMetaDataEvent constructor.
     *
     * @param string $level   A log level as defined in Psr\Log\LogLevel
     * @param string $message
     */
    public function __construct(string $level, string $message)
    {
        $this->level = $level;
        $this->message = $message;
    }

    /**
     * @return string Return a log level as defined in Psr\Log\LogLevel
     */
    public function getLevel(): string
    {
        return $this->level;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
