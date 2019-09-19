<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Admin;

use OxidEsales\EshopCommunity\Tests\Acceptance\AdminTestCase;
use OxidEsales\TestingLibrary\helpers\ExceptionLogFileHelper;
use Webmozart\PathUtil\Path;
use OxidEsales\Facts\Config\ConfigFile as FactsConfigFile;

/**
 * Class QueryLoggerTest
 *
 * @package OxidEsales\EshopCommunity\Tests\Acceptance\Admin
 */
class QueryLoggerTest extends AdminTestCase
{

    /**
     * @var ExceptionLogFileHelper
     */
    private $adminLogHelper;

    protected function setUp()
    {
        parent::setUp();

        $this->skipTestForDisabledAdminQueryLog();

        $this->adminLogHelper = new ExceptionLogFileHelper(Path::join(OX_BASE_PATH, 'log', 'oxadmin.log'));
        $this->adminLogHelper->clearExceptionLogFile();
    }

    /**
     * Verify that shop frontend is ok with enabled admin log.
     *
     * @group adminquerylog
     */
    public function testShopFrontendWithAdminLogEnabled()
    {
        $this->openShop();
        $this->checkForErrors();

        $this->assertEmpty($this->adminLogHelper->getExceptionLogFileContent());
    }

    /**
     * Verify that shop admin is ok with enabled admin log.
     *
     * @group adminquerylog
     */
    public function testShopAdminWithAdminLogEnabled()
    {
        $this->loginAdmin('Master Settings', 'Core Settings');
        $this->adminLogHelper->clearExceptionLogFile();

        $this->openTab("Settings");
        $this->click("link=Other settings");
        $this->assertTextPresent('Mandatory fields in User Registration Form');
        $this->clickAndWait("//form[@id='myedit']/input[@name='save']");

        $logged = $this->adminLogHelper->getExceptionLogFileContent();

        $this->assertContains('query:', strtolower($logged));
        $this->assertContains('function: saveshopconfvar', strtolower($logged));
    }

    /**
     * Tests here can only work if blLogChangesInAdmin is set in config.inc.php.
     * Setting the flag in Config during test setup will not work.
     */
    public function skipTestForDisabledAdminQueryLog()
    {
        $factsConfigFile = new FactsConfigFile();
        if (!$factsConfigFile->getVar('blLogChangesInAdmin')) {
            $this->markTestSkipped('Test needs blLogChangesInAdmin flag in config.inc.php set as true');
        }
    }
}
