<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Edition;

use OxidEsales\EshopCommunity\Core\Edition\EditionSelector;
use OxidEsales\TestingLibrary\UnitTestCase;
use oxRegistry;
use OxidEsales\Eshop\Core\ConfigFile;

// TODO: class should be refactored to testable state.
class EditionSelectorTest extends UnitTestCase
{
    public function testReturnsEdition()
    {
        $editionSelector = new EditionSelector();

        $this->assertSame($this->getConfig()->getEdition(), $editionSelector->getEdition());
    }

    public function testCheckActiveEdition()
    {
        if ($this->getTestConfig()->getShopEdition() !== 'CE') {
            $this->markTestSkipped('This test is for Community editions only.');
        }

        $editionSelector = new EditionSelector();

        $this->assertSame('CE', $editionSelector->getEdition());
        $this->assertTrue($editionSelector->isCommunity());
        $this->assertFalse($editionSelector->isEnterprise());
        $this->assertFalse($editionSelector->isProfessional());
    }

    public function providerReturnsForcedEdition()
    {
        return array(
            array(EditionSelector::ENTERPRISE, 'EE'),
            array(EditionSelector::PROFESSIONAL, 'PE'),
            array(EditionSelector::COMMUNITY, 'CE'),
        );
    }

    /**
     * @dataProvider providerReturnsForcedEdition
     */
    public function testReturnsForcedEdition($editionToForce, $expectedEdition)
    {
        $editionSelector = new EditionSelector($editionToForce);

        $this->assertSame($expectedEdition, $editionSelector->getEdition());
    }

    public function testIsEnterpriseReturnTrueIfForced()
    {
        $editionSelector = new EditionSelector(EditionSelector::ENTERPRISE);
        $this->assertTrue($editionSelector->isEnterprise());
        $this->assertFalse($editionSelector->isProfessional());
        $this->assertFalse($editionSelector->isCommunity());
    }

    public function testIsProfessionalReturnTrueIfForced()
    {
        $editionSelector = new EditionSelector(EditionSelector::PROFESSIONAL);
        $this->assertTrue($editionSelector->isProfessional());
        $this->assertFalse($editionSelector->isEnterprise());
        $this->assertFalse($editionSelector->isCommunity());
    }

    public function testIsCommunityReturnTrueIfForced()
    {
        $editionSelector = new EditionSelector(EditionSelector::COMMUNITY);
        $this->assertTrue($editionSelector->isCommunity());
        $this->assertFalse($editionSelector->isEnterprise());
        $this->assertFalse($editionSelector->isProfessional());
    }

    public function testForcingEditionByConfig()
    {
        $configFile = oxRegistry::get('oxConfigFile');
        $configFile->setVar('edition', 'EE');

        $editionSelector = new EditionSelector();
        $this->assertTrue($editionSelector->isEnterprise());
        $this->assertFalse($editionSelector->isCommunity());
        $this->assertFalse($editionSelector->isProfessional());
    }

    public function testForcingEditionByConfigWorksWithLowerCase()
    {
        $configFile = oxRegistry::get('oxConfigFile');
        $configFile->setVar('edition', 'ee');

        $editionSelector = new EditionSelector();
        $this->assertTrue($editionSelector->isEnterprise());
        $this->assertFalse($editionSelector->isCommunity());
        $this->assertFalse($editionSelector->isProfessional());
    }

    /**
     * When oxConfigFile is not registered in registry (happens during setup), it should be created on the fly.
     */
    public function testForcingEditionByConfigWhenNotRegistered()
    {
        $path = $this->createFile('config.inc.php', '<?php $this->edition = "EE";');
        $fakeConfigFile = new ConfigFile($path);

        $configFile = oxRegistry::get(\OxidEsales\Eshop\Core\ConfigFile::class);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\ConfigFile::class, $fakeConfigFile);

        $editionSelector = new EditionSelector();
        $this->assertTrue($editionSelector->isEnterprise());
        $this->assertFalse($editionSelector->isCommunity());
        $this->assertFalse($editionSelector->isProfessional());

        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\ConfigFile::class, $configFile);
    }
}
