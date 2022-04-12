<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge;

use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Generator\RandomTokenGeneratorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\SystemRequirements\SystemSecurityCheckerInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\FallbackTokenGenerator;
use Psr\Log\LoggerInterface;

class RandomTokenGeneratorBridge implements RandomTokenGeneratorBridgeInterface
{
    private RandomTokenGeneratorInterface $randomTokenGenerator;
    private SystemSecurityCheckerInterface $systemSecurityChecker;
    private FallbackTokenGenerator $fallbackTokenGenerator;
    private LoggerInterface $logger;

    public function __construct(
        RandomTokenGeneratorInterface $randomTokenGenerator,
        SystemSecurityCheckerInterface $systemSecurityChecker,
        FallbackTokenGenerator $fallbackTokenGenerator,
        LoggerInterface $logger
    ) {
        $this->randomTokenGenerator = $randomTokenGenerator;
        $this->systemSecurityChecker = $systemSecurityChecker;
        $this->fallbackTokenGenerator = $fallbackTokenGenerator;
        $this->logger = $logger;
    }

    /** @inheritdoc */
    public function getAlphanumericToken(int $length): string
    {
        return $this->randomTokenGenerator->getAlphanumericToken($length);
    }

    public function getHexToken(int $length): string
    {
        return $this->randomTokenGenerator->getHexToken($length);
    }

    /** @inheritDoc */
    public function getHexTokenWithFallback(int $length): string
    {
        if (!$this->systemSecurityChecker->isCryptographicallySecure()) {
            $this->logger->warning(
                'No appropriate source of randomness was found! Please re-configure your system to enable generation of cryptographically secure values.'
            );
            return $this->fallbackTokenGenerator->getHexToken($length);
        }
        return $this->randomTokenGenerator->getHexToken($length);
    }
}
