<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance;

use \OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Edition\EditionSelector;
use OxidEsales\TestingLibrary\TestSqlPathProvider;

abstract class AcceptanceTestCase extends \OxidEsales\TestingLibrary\AcceptanceTestCase
{
    protected $preventModuleVersionNotify = true;

    /**
     * Sets up default environment for tests.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->activateTheme('azure');

        //Suppress check for new module versions on every admin login
        if ($this->preventModuleVersionNotify) {
            $aParams = array("type" => "bool", "value" => true);
            $this->callShopSC("oxConfig", null, null, array('preventModuleVersionNotify' => $aParams));
        }

        $this->activateTheme('azure');
        $this->clearCache();
    }

    /**
     * @inheritdoc
     */
    public function setUpTestsSuite($testSuitePath)
    {
        parent::setUpTestsSuite($testSuitePath);
    }

    /**
     * Adds tests sql data to database.
     *
     * @param string $sTestSuitePath
     */
    public function addTestData($sTestSuitePath)
    {
        parent::addTestData($sTestSuitePath);

        $editionSelector = new EditionSelector();

        if ($editionSelector->isEnterprise()) {
            $testSqlPathProvider = new TestSqlPathProvider(new EditionSelector(), $this->getTestConfig()->getShopPath());
            $sTestSuitePath = realpath($testSqlPathProvider->getDataPathBySuitePath($sTestSuitePath));

            $sFileName = $sTestSuitePath . '/demodata_' . SHOP_EDITION . '.sql';
            if (file_exists($sFileName)) {
                $this->importSql($sFileName);
            }

            if (isSUBSHOP && file_exists($sTestSuitePath . '/demodata_EE_mall.sql')) {
                $this->importSql($sTestSuitePath . '/demodata_EE_mall.sql');
            }
        }
        $this->resetConfig();
    }

    /**
     * Reset config to have newest information from database.
     * SQL files might contain configuration changes.
     * Base object has static cache for Config object.
     * Config object has cache for database configuration.
     *
     */
    private function resetConfig()
    {
        $config = Registry::getConfig();
        $config->reinitialize();
        /** Reset static variable in oxSuperCfg class, which is base class for every class. */
        $config->setConfig($config);
    }
}
