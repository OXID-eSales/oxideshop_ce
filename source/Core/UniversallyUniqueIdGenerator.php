<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * Class oxUniversallyUniqueIdGenerator used as universally unique id generator.
 */
class UniversallyUniqueIdGenerator
{
    /**
     * @var \OxidEsales\Eshop\Core\OpenSSLFunctionalityChecker
     */
    private $_openSSLChecker;

    /**
     * Sets dependencies.
     *
     * @param \OxidEsales\Eshop\Core\OpenSSLFunctionalityChecker $openSSLChecker
     */
    public function __construct(\OxidEsales\Eshop\Core\OpenSSLFunctionalityChecker $openSSLChecker = null)
    {
        if (is_null($openSSLChecker)) {
            $openSSLChecker = oxNew(\OxidEsales\Eshop\Core\OpenSSLFunctionalityChecker::class);
        }
        $this->_openSSLChecker = $openSSLChecker;
    }

    /**
     * Generates UUID based on either openSSL's openssl_random_pseudo_bytes or mt_rand.
     *
     * @return string
     */
    public function generate()
    {
        $sSeed = $this->generateV4();

        return $this->generateV5($sSeed, php_uname('n'));
    }

    /**
     * Generates version 4 UUID.
     *
     * @return string
     */
    public function generateV4()
    {
        if ($this->_getOpenSSLChecker()->isOpenSslRandomBytesGeneratorAvailable()) {
            return $this->_generateBasedOnOpenSSL();
        }

        return $this->_generateBasedOnMtRand();
    }

    /**
     * Generates version 5 UUID.
     *
     * @param string $sSeed
     * @param string $sSalt
     *
     * @return string
     */
    public function generateV5($sSeed, $sSalt)
    {
        $sSeed = str_replace(['-', '{', '}'], '', $sSeed);
        $sBinarySeed = '';
        for ($i = 0; $i < strlen($sSeed); $i += 2) {
            $sBinarySeed .= chr(hexdec($sSeed[$i] . $sSeed[$i + 1]));
        }
        $sHash = sha1($sBinarySeed . $sSalt);
        $sUUID = sprintf(
            '%08s-%04s-%04x-%04x-%12s',
            substr($sHash, 0, 8),
            substr($sHash, 8, 4),
            (hexdec(substr($sHash, 12, 4)) & 0x0fff) | 0x3000,
            (hexdec(substr($sHash, 16, 4)) & 0x3fff) | 0x8000,
            substr($sHash, 20, 12)
        );

        return $sUUID;
    }

    /**
     * gets open SSL checker.
     *
     * @return \OxidEsales\Eshop\Core\OpenSSLFunctionalityChecker
     */
    protected function _getOpenSSLChecker()
    {
        return $this->_openSSLChecker;
    }

    /**
     * Generates UUID based on OpenSSL's openssl_random_pseudo_bytes.
     *
     * @return string
     */
    protected function _generateBasedOnOpenSSL()
    {
        $sRandomData = openssl_random_pseudo_bytes(16);
        $sRandomData[6] = chr(ord($sRandomData[6]) & 0x0f | 0x40); // set version to 0100
        $sRandomData[8] = chr(ord($sRandomData[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($sRandomData), 4));
    }

    /**
     * Generates UUID based on mt_rand.
     *
     * @return string
     */
    protected function _generateBasedOnMtRand()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}
