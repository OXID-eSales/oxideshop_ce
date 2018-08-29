<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Exception;

/**
 * exception class for non existing results found
 */
class NoResultException extends \OxidEsales\Eshop\Core\Exception\StandardException
{
    /**
     * Exception type.
     *
     * @var string
     */
    protected $type = 'NoResultException';
}
