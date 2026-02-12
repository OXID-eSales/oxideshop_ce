<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Authentication\Session;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

interface SessionUserProviderInterface
{
    public function loadSessionUser(Request $request): UserInterface;
}
