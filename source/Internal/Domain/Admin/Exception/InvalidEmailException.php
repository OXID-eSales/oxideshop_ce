<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Admin\Exception;

use function sprintf;

class InvalidEmailException extends \Exception
{
    public function __construct(string $email)
    {
        parent::__construct(sprintf('Provided email string %s is not a valid email.', $email));
    }
}
