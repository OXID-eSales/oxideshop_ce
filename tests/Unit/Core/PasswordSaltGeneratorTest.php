<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use \oxPasswordSaltGenerator;

class PasswordSaltGeneratorTest extends \OxidTestCase
{
    public function providerOpenSslRandomBytesGeneratorAvailability()
    {
        return array(
            array(true),
            array(false)
        );
    }

    /**
     * @dataProvider providerOpenSslRandomBytesGeneratorAvailability
     */
    public function testSaltLength($blIsOpenSslRandomBytesGeneratorAvailable)
    {
        $oOpenSSLFunctionalityChecker = $this->_getOpenSSLFunctionalityChecker($blIsOpenSslRandomBytesGeneratorAvailable);
        $oGenerator = new oxPasswordSaltGenerator($oOpenSSLFunctionalityChecker);
        $this->assertSame(32, strlen($oGenerator->generate()));
    }

    /**
     * @dataProvider providerOpenSslRandomBytesGeneratorAvailability
     */
    public function testGeneratedSaltShouldBeUnique($blIsOpenSslRandomBytesGeneratorAvailable)
    {
        $oOpenSSLFunctionalityChecker = $this->_getOpenSSLFunctionalityChecker($blIsOpenSslRandomBytesGeneratorAvailable);
        $oGenerator = new oxPasswordSaltGenerator($oOpenSSLFunctionalityChecker);
        $aSalts = array();

        for ($i = 1; $i <= 100; $i++) {
            $aSalts[] = $oGenerator->generate();
        }

        $this->assertSame(100, count(array_unique($aSalts)));
    }

    /**
     * Returns oxOpenSSLFunctionalityChecker object dependent on condition. It can return mocked object or not.
     * This is needed because of environment. For example on php 5.2 there is no such function like openssl_random_pseudo_bytes
     * so in that case we don't want to mock checker.
     *
     * @param $blIsOpenSslRandomBytesGeneratorAvailable
     *
     * @return oxOpenSSLFunctionalityChecker
     */
    private function _getOpenSSLFunctionalityChecker($blIsOpenSslRandomBytesGeneratorAvailable)
    {
        if ($blIsOpenSslRandomBytesGeneratorAvailable) {
            $oOpenSSLFunctionalityChecker = oxNew('oxOpenSSLFunctionalityChecker');
        } else {
            /** @var oxOpenSSLFunctionalityChecker $oOpenSSLFunctionalityChecker */
            $oOpenSSLFunctionalityChecker = $this->getMock(\OxidEsales\Eshop\Core\OpenSSLFunctionalityChecker::class, array('isOpenSslRandomBytesGeneratorAvailable'));
            $oOpenSSLFunctionalityChecker->expects($this->any())->method('isOpenSslRandomBytesGeneratorAvailable')->will($this->returnValue($blIsOpenSslRandomBytesGeneratorAvailable));
        }


        return $oOpenSSLFunctionalityChecker;
    }
}
