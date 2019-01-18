<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\MetaData\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * @internal
 */
class BadMetaDataFoundEvent extends Event
{
    /**
     * Name of the event
     */
    const NAME = self::class;

    /**
     * @var string
     */
    private $metaDataFilePath;

    /**
     * @var string
     */
    private $message;

    /**
     * BadMetaDataFoundEvent constructor.
     *
     * @param string $metaDataFilePath
     * @param string $message
     */
    public function __construct(string $metaDataFilePath, string $message)
    {
        $this->metaDataFilePath = $metaDataFilePath;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMetaDataFilePath(): string
    {
        return $this->metaDataFilePath;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
