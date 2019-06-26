<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use \oxUniversallyUniqueIdGenerator;

/**
 * Class Unit_Core_oxServersClusterIdGeneratorTest
 */
class UniversallyUniqueIdGeneratorTest extends \OxidTestCase
{
    public function testUUIDUniqueness()
    {
        $oGenerator = oxNew('oxUniversallyUniqueIdGenerator');

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
        $oGenerator = oxNew('oxUniversallyUniqueIdGenerator');
        $sId = $oGenerator->generate();

        $this->assertRegExp('/^[\w]{8}-[\w]{4}-[\w]{4}-[\w]{4}-[\w]{12}$/', $sId);
    }

    public function testUUIDV4Uniqueness()
    {
        $oGenerator = oxNew('oxUniversallyUniqueIdGenerator');

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
        $oCheckerMock = $this->getMock(\OxidEsales\Eshop\Core\OpenSSLFunctionalityChecker::class, array('isOpenSslRandomBytesGeneratorAvailable'));
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
        $oGenerator = oxNew('oxUniversallyUniqueIdGenerator');
        $sId = $oGenerator->generateV4();

        $this->assertRegExp('/^[\w]{8}-[\w]{4}-[\w]{4}-[\w]{4}-[\w]{12}$/', $sId);
    }

    /**
     * Generating UUID v5 with different salt should yield different result.
     */
    public function testUUIDV5UniquenessWithDifferentSalt()
    {
        $oGenerator = oxNew('oxUniversallyUniqueIdGenerator');

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
        $oGenerator = oxNew('oxUniversallyUniqueIdGenerator');

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
        $oGenerator = oxNew('oxUniversallyUniqueIdGenerator');

        $sId1 = $oGenerator->generateV5('seed', 'salt');
        $sId2 = $oGenerator->generateV5('seed', 'salt');

        $this->assertEquals($sId1, $sId2);
    }

    /**
     * Generated v5 UUID should consist of 5 word character groups separated by dashes.
     */
    public function testUUIDV5Structure()
    {
        $oGenerator = oxNew('oxUniversallyUniqueIdGenerator');
        $sId = $oGenerator->generateV5('seed', 'salt');

        $this->assertRegExp('/^[\w]{8}-[\w]{4}-[\w]{4}-[\w]{4}-[\w]{12}$/', $sId);
    }
}
