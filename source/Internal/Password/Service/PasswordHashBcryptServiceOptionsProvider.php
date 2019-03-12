<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Password\Service;

use OxidEsales\EshopCommunity\Internal\Password\Exception\PasswordHashException;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;

/**
 * @internal
 */
class PasswordHashBcryptServiceOptionsProvider implements PasswordHashServiceOptionsProviderInterface
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
     * @return array
     */
    public function getOptions(): array
    {
        $cost = $this->context->getPasswordHashingBcryptCost();
        $this->validateCostOption($cost);

        $options = [
            /* 'salt' => '', the salt option is deprecated for security reasons and must not be used **/
            'cost' => $cost,
        ];

        return $options;
    }


    /**
     * @param int $cost
     *
     * @throws PasswordHashException
     */
    private function validateCostOption(int $cost)
    {
        if ($cost < 4) {
            throw new PasswordHashException('The cost option for bcrypt must not be smaller than 4.');
        }
        if ($cost > 31) {
            throw new PasswordHashException('The cost option for bcrypt must not be bigger than 31.');
        }
    }
}
