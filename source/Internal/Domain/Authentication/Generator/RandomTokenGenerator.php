<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Authentication\Generator;

use Exception;
use InvalidArgumentException;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Exception\UnavailableSourceOfRandomnessException;

use function base64_encode;
use function bin2hex;
use function random_bytes;
use function sprintf;
use function str_replace;
use function strlen;
use function substr;

class RandomTokenGenerator implements RandomTokenGeneratorInterface
{
    private const BASE_64_NON_ALPHANUMERIC_CHARACTERS = ['+', '/', '='];
    private const MINIMUM_TOKEN_LENGTH = 8;

    /** @inheritDoc */
    public function getAlphanumericToken(int $length): string
    {
        $this->validateTokenLength($length);

        $token = '';
        while (strlen($token) < $length) {
            $token .= $this->getAlphanumericString($length);
        }
        return substr($token, 0, $length);
    }

    /** @inheritDoc */
    public function getHexToken(int $length): string
    {
        $this->validateTokenLength($length);

        return substr($this->getHexString($length), 0, $length);
    }

    private function getAlphanumericString(int $length): string
    {
        $base64String = base64_encode(
            $this->getRandomBytes($length)
        );
        return $this->removeNonAlphanumericCharacters($base64String);
    }

    private function getHexString(int $length): string
    {
        return bin2hex(
            $this->getRandomBytes($length)
        );
    }

    private function removeNonAlphanumericCharacters(string $base64string): string
    {
        return str_replace(
            self::BASE_64_NON_ALPHANUMERIC_CHARACTERS,
            '',
            $base64string
        );
    }

    private function validateTokenLength(int $length): void
    {
        if ($length < self::MINIMUM_TOKEN_LENGTH) {
            throw new InvalidArgumentException(
                sprintf('Token length must be at least %d characters.', self::MINIMUM_TOKEN_LENGTH)
            );
        }
    }

    private function getRandomBytes(int $length): string
    {
        try {
            return random_bytes($length);
        } catch (Exception $exception) {
            throw new UnavailableSourceOfRandomnessException(
                message: $exception->getMessage(),
                previous: $exception
            );
        }
    }
}
