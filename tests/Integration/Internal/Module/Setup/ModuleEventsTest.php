<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\Setup;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Service\ModuleActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\TestData\TestModule\ModuleEvents;
use OxidEsales\TestingLibrary\Services\Library\DatabaseRestorer\DatabaseRestorer;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ModuleEventsTest extends TestCase
{
    use ContainerTrait;

    private $shopId = 1;
    private $testModuleId = 'testModuleId';

    /**
     * @var DatabaseRestorer
     */
    private $databaseRestorer;

    public function setUp()
    {
        $this->databaseRestorer = new DatabaseRestorer();
        $this->databaseRestorer->dumpDB(__CLASS__);

        parent::setUp();
    }

    protected function tearDown()
    {
        $this->databaseRestorer->restoreDB(__CLASS__);

        parent::tearDown();
    }

    public function testActivationEventWasExecuted()
    {
        $moduleConfiguration = $this->getTestModuleConfiguration();
        $moduleConfiguration->addSetting(new ModuleSetting(
            ModuleSetting::EVENTS,
            [
                'onActivate'    => ModuleEvents::class . '::onActivate',
            ]
        ));

        $this->persistModuleConfiguration($moduleConfiguration);

        /** @var ModuleActivationServiceInterface $moduleActivationService */
        $moduleActivationService = $this->get(ModuleActivationServiceInterface::class);

        ob_start();
        $moduleActivationService->activate($this->testModuleId, $this->shopId);
        $eventMessage = ob_get_contents();
        ob_end_clean();

        $this->assertSame('Method onActivate was called', $eventMessage);
    }

    public function testActivationEventWasExecutedSecondTime()
    {
        $moduleConfiguration = $this->getTestModuleConfiguration();
        $moduleConfiguration->addSetting(new ModuleSetting(
            ModuleSetting::EVENTS,
            [
                'onActivate'    => ModuleEvents::class . '::onActivate',
            ]
        ));

        $this->persistModuleConfiguration($moduleConfiguration);

        /** @var ModuleActivationServiceInterface $moduleActivationService */
        $moduleActivationService = $this->get(ModuleActivationServiceInterface::class);

        ob_start();
        $moduleActivationService->activate($this->testModuleId, $this->shopId);
        ob_end_clean();

        $moduleActivationService->deactivate($this->testModuleId, $this->shopId);

        ob_start();
        $moduleActivationService->activate($this->testModuleId, $this->shopId);
        $eventMessage = ob_get_contents();
        ob_end_clean();

        $this->assertSame('Method onActivate was called', $eventMessage);
    }


    public function testDeactivationEventWasExecuted()
    {
        $moduleConfiguration = $this->getTestModuleConfiguration();
        $moduleConfiguration->addSetting(new ModuleSetting(
            ModuleSetting::EVENTS,
            [
                'onDeactivate'  => ModuleEvents::class . '::onDeactivate',
            ]
        ));

        $this->persistModuleConfiguration($moduleConfiguration);

        /** @var ModuleActivationServiceInterface $moduleActivationService */
        $moduleActivationService = $this->get(ModuleActivationServiceInterface::class);

        $moduleActivationService->activate($this->testModuleId, $this->shopId);

        ob_start();
        $moduleActivationService->deactivate($this->testModuleId, $this->shopId);
        $eventMessage = ob_get_contents();
        ob_end_clean();

        $this->assertSame('Method onDeactivate was called', $eventMessage);
    }

    private function getTestModuleConfiguration(): ModuleConfiguration
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId($this->testModuleId);
        $moduleConfiguration->setPath('TestModule');

        return $moduleConfiguration;
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     */
    private function persistModuleConfiguration(ModuleConfiguration $moduleConfiguration)
    {
        $shopConfiguration = new ShopConfiguration();
        $shopConfiguration->addModuleConfiguration($moduleConfiguration);

        $this->get(ShopConfigurationDaoInterface::class)->save(
            $shopConfiguration,
            $this->shopId,
            $this->getEnvironment()
        );
    }

    private function getEnvironment(): string
    {
        return $this->get(ContextInterface::class)->getEnvironment();
    }
}
