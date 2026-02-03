<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Mailing\Factory;

use Symfony\Component\Mailer\Transport\TransportInterface;

interface TransportFactoryInterface
{
    public function create(): TransportInterface;
}
