<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Password\Service;

use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;

/**
 * @internal
 */
class PasswordHashBcryptServiceOptionsProvider
{
    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * PasswordHashBcryptServiceOptionsProvider constructor.
     *
     * @param ContextInterface $context
     */
    public function __construct(ContextInterface $context)
    {
        $this->context = $context;
    }

    /**
     * @return int
     */
    public function getCost(): int
    {
        return $this->context->getPasswordHashingBcryptCost();
    }
}
