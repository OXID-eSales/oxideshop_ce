<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Common\Exception;

use Throwable;

/**
 * @internal
 *
 */
class DirectoryExistentException extends \Exception
{
    /** @var string $directoryAlreadyExistent */
    private $directoryAlreadyExistent = '';


    /**
     * DirectoryExistentException constructor.
     *
     * @param string         $directoryAlreadyExistent
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(
        string $directoryAlreadyExistent,
        string $message = "",
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->directoryAlreadyExistent = $directoryAlreadyExistent;
    }

    /**
     * @return string
     */
    public function getDirectoryAlreadyExistent(): string
    {
        return $this->directoryAlreadyExistent;
    }


}
