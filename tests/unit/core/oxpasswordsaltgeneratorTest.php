<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

class Unit_Core_oxPasswordSaltGeneratorTest extends OxidTestCase
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

        for($i=1; $i<=100; $i++)
        {
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
            $oOpenSSLFunctionalityChecker = new oxOpenSSLFunctionalityChecker();
        } else {
            /** @var oxOpenSSLFunctionalityChecker $oOpenSSLFunctionalityChecker */
            $oOpenSSLFunctionalityChecker = $this->getMock('oxOpenSSLFunctionalityChecker', array('isOpenSslRandomBytesGeneratorAvailable'));
            $oOpenSSLFunctionalityChecker->expects($this->any())->method('isOpenSslRandomBytesGeneratorAvailable')->will($this->returnValue($blIsOpenSslRandomBytesGeneratorAvailable));
        }


        return $oOpenSSLFunctionalityChecker;
    }
}
