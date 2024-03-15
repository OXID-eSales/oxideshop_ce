<?php

namespace OxidEsales\EshopCommunity\Core;

/**
 * Provides cryptographically secure random number generator
 * Currently uses PHPs random_bytes() and random_int()
 */
class RandomNumberGenerator
{
    /** Returns cryptographically secure random bytes
     * @param int $length The length of the random string that should be returned in bytes; must be 1 or greater.
     * @return string A string containing the requested number of cryptographically secure random bytes.
     * @throws \Exception Exception if thrown if the random number generator is not working
     */
    public function getRandomBytes(int $length): string
    {
        if ($length < 1) {
            throw new \Exception('length must must be 1 or greater');
        }

        return random_bytes($length);
    }

    /** Calls getRandomBytes() and passes the result through bin2hex()
     * @param int $length The length of the returned string; must be 1 or greater.
     * @return string
     * @throws \Exception Exception if thrown if the random number generator is not working
     */
    public function getRandomHexString(int $length): string
    {
        if ($length < 1) {
            throw new \Exception('length must be 1 or greater');
        }

        $randomBytesLength = ceil($length / 2);
        $hexStr = bin2hex($this->getRandomBytes($randomBytesLength));
        return substr($hexStr, 0, $length);
    }

    /**
     * @param int $min The lowest value to be returned.
     * @param int $max The highest value to be returned.
     * @return int A cryptographically secure, uniformly selected integer from the closed interval [min, max]. Both min and max are possible return values.
     * @throws \Exception Exception if thrown if the random number generator is not working
     */
    public function getRandomInt(int $min, int $max): int
    {
        return random_int($min, $max);
    }
}