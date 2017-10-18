<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Exception;

/**
 * e.g.:
 * - no match for requested controller id
 *
 */
class RoutingException extends \OxidEsales\Eshop\Core\Exception\StandardException
{
    /**
     * Exception type
     *
     * @var string
     */
    protected $type = 'RoutingException';

    /**
     * RoutingException constructor.
     *
     * This exception is thrown in case no controller class can be found for a supplied controller Id.
     *
     * @param string $controllerId
     */
    public function __construct($controllerId)
    {
        $message = sprintf('No controller defined for id %s', $controllerId);
        parent::__construct($message);
    }
}
