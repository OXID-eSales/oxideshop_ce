<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Authentication\Session\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

class NoActiveSessionUserException extends AuthenticationException
{
}
