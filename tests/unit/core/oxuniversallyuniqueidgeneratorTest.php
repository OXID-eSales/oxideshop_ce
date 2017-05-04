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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

/**
 * Class Unit_Core_oxServersClusterIdGeneratorTest
 *
 * @covers oxServerNodesManager
 */
class Unit_Core_oxUniversallyUniqueIdGeneratorTest extends OxidTestCase
{

    public function testUUIDUniqueness()
    {
        $oGenerator = new oxUniversallyUniqueIdGenerator();

        $aIds = array();
        for ($i = 0; $i < 100; $i++) {
            $aIds[] = $oGenerator->generate();
        }

        $this->assertEquals(100, count(array_unique($aIds)));
    }

    /**
     * Generated UUID should consist of 5 word character groups separated by dashes.
     */
    public function testUUIDStructure()
    {
        $oGenerator = new oxUniversallyUniqueIdGenerator();
        $sId = $oGenerator->generate();

        $this->assertRegExp('/^[\w]{8}-[\w]{4}-[\w]{4}-[\w]{4}-[\w]{12}$/', $sId);
    }

    public function testUUIDV4Uniqueness()
    {
        $oGenerator = new oxUniversallyUniqueIdGenerator();

        $aIds = array();
        for ($i = 0; $i < 100; $i++) {
            $aIds[] = $oGenerator->generateV4();
        }

        $this->assertEquals(100, count(array_unique($aIds)));
    }

    /**
     * V4 UUID can be generated based either on openSSL or mt_rand.
     * mt_rand is used when openSSL is not available.
     */
    public function testUUIDV4UniquenessWhenInFallbackMode()
    {
        $oCheckerMock = $this->getMock('oxOpenSSLFunctionalityChecker', array('isOpenSslRandomBytesGeneratorAvailable'));
        $oCheckerMock->expects($this->any())->method('isOpenSslRandomBytesGeneratorAvailable')->will($this->returnValue(false));
        /** @var oxOpenSSLFunctionalityChecker $oChecker */
        $oChecker = $oCheckerMock;

        $oGenerator = new oxUniversallyUniqueIdGenerator($oChecker);

        $aIds = array();
        for ($i = 0; $i < 100; $i++) {
            $aIds[] = $oGenerator->generateV4();
        }

        $this->assertEquals(100, count(array_unique($aIds)));
    }

    /**
     * Generated v4 UUID should consist of 5 word character groups separated by dashes.
     */
    public function testUUIDV4Structure()
    {
        $oGenerator = new oxUniversallyUniqueIdGenerator();
        $sId = $oGenerator->generateV4();

        $this->assertRegExp('/^[\w]{8}-[\w]{4}-[\w]{4}-[\w]{4}-[\w]{12}$/', $sId);
    }

    /**
     * Generating UUID v5 with different salt should yield different result.
     */
    public function testUUIDV5UniquenessWithDifferentSalt()
    {
        $oGenerator = new oxUniversallyUniqueIdGenerator();

        $aIds = array();
        for ($i = 0; $i < 100; $i++) {
            $aIds[] = $oGenerator->generateV5('seed', 'salt' . $i);
        }

        $this->assertEquals(100, count(array_unique($aIds)));
    }

    /**
     * Generating UUID v5 with different seed should yield different result.
     */
    public function testUUIDV5UniquenessWithDifferentSeed()
    {
        $oGenerator = new oxUniversallyUniqueIdGenerator();

        $aIds = array();
        for ($i = 0; $i < 100; $i++) {
            $aIds[] = $oGenerator->generateV5('seed' . $i, 'salt');
        }

        $this->assertEquals(100, count(array_unique($aIds)));
    }

    /**
     * Generating UUID v5 with same seed and salt should yield same result.
     */
    public function testUUIDV5EqualityWithSameSeedAndSalt()
    {
        $oGenerator = new oxUniversallyUniqueIdGenerator();

        $sId1 = $oGenerator->generateV5('seed', 'salt');
        $sId2 = $oGenerator->generateV5('seed', 'salt');

        $this->assertEquals($sId1, $sId2);
    }

    /**
     * Generated v5 UUID should consist of 5 word character groups separated by dashes.
     */
    public function testUUIDV5Structure()
    {
        $oGenerator = new oxUniversallyUniqueIdGenerator();
        $sId = $oGenerator->generateV5('seed', 'salt');

        $this->assertRegExp('/^[\w]{8}-[\w]{4}-[\w]{4}-[\w]{4}-[\w]{12}$/', $sId);
    }
}