<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge;

use OxidEsales\Eshop\Core\OpenSSLFunctionalityChecker;
use OxidEsales\Eshop\Core\PasswordSaltGenerator;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Generator\RandomTokenGeneratorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\SystemRequirements\SystemSecurityCheckerInterface;
use Psr\Log\LoggerInterface;

final class RandomTokenGeneratorBridge implements RandomTokenGeneratorBridgeInterface
{
    private RandomTokenGeneratorInterface $randomTokenGenerator;
    private SystemSecurityCheckerInterface $systemSecurityChecker;
    private LoggerInterface $logger;

    public function __construct(
        RandomTokenGeneratorInterface $randomTokenGenerator,
        SystemSecurityCheckerInterface $systemSecurityChecker,
        LoggerInterface $logger
    ) {
        $this->randomTokenGenerator = $randomTokenGenerator;
        $this->systemSecurityChecker = $systemSecurityChecker;
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
            return $this->getFallbackHexToken($length);
        }
        return $this->randomTokenGenerator->getHexToken($length);
    }

    private function getFallbackHexToken(int $length)
    {
        $generator = oxNew(
            PasswordSaltGenerator::class,
            oxNew(OpenSSLFunctionalityChecker::class)
        );
        $token = '';
        while (strlen($token) < $length) {
            $token .= $generator->generate();
        }
        return substr($token, 0, $length);
    }
}
