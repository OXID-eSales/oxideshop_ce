<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\Eshop\Core\UtilsObject;

function isAdmin(): bool
{
    return defined('OX_IS_ADMIN') && OX_IS_ADMIN;
}

/**
 * Creates and returns new object. If creation is not available, dies and outputs
 * error message.
 *
 * @template T
 * @param class-string<T> $className
 * param mixed  ...$args   constructor arguments
 *
 * @return T
 */
function oxNew(string $className, ...$args)
{
    startProfile('oxNew');
    $object = call_user_func_array([UtilsObject::getInstance(), 'oxNew'], array_merge([$className], $args));
    stopProfile('oxNew');

    return $object;
}
