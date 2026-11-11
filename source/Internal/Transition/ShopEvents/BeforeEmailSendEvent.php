<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\ShopEvents;

use OxidEsales\Eshop\Core\Email;
use Symfony\Contracts\EventDispatcher\Event;

class BeforeEmailSendEvent extends Event
{
    public function __construct(private readonly Email $email)
    {
    }

    public function getEmail(): Email
    {
        return $this->email;
    }
}
