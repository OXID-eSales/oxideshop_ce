<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\Email;

use OxidEsales\EshopCommunity\Core\Email;
use Symfony\Component\Mime\Email as SymfonyEmail;

interface EmailAdapterInterface
{
    public function convertToSymfonyEmail(Email $legacyEmail): SymfonyEmail;
}
