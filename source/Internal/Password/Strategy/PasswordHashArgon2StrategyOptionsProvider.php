<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Password\Strategy;

use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;

/**
 * Class PasswordHashArgon2StrategyOptionsProvider
 *
 * @package OxidEsales\EshopCommunity\Internal\Password\Service
 */
class PasswordHashArgon2StrategyOptionsProvider implements PasswordHashStrategyOptionsProviderInterface
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
        $memoryCost = $this->context->getPasswordHashingArgon2MemoryCost();
        $this->validateMemoryCostOption($memoryCost);

        $timeCost = $this->context->getPasswordHashingArgon2TimeCost();
        $this->validateTimeCostOption($timeCost);

        $threads = $this->context->getPasswordHashingArgon2Threads();
        $this->validateThreadsOption($threads);

        $options = [
            'memory_cost' => $memoryCost,
            'time_cost'   => $timeCost,
            'threads'     => $threads
        ];

        return $options;
    }

    /**
     * @param int $memoryCost
     */
    private function validateMemoryCostOption(int $memoryCost)
    {
    }

    /**
     * @param int $timeCost
     */
    private function validateTimeCostOption(int $timeCost)
    {
    }

    /**
     * @param int $threads
     */
    private function validateThreadsOption(int $threads)
    {
    }
}
