<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Command;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\ModuleSetupException;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Service\ModuleActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command activates module by module id.
 * @internal
 */
class ModuleActivateCommand extends Command
{
    const MESSAGE_MODULE_ALREADY_ACTIVE = 'Module - "%s" already active.';

    const MESSAGE_MODULE_ACTIVATED = 'Module - "%s" was activated.';

    const MESSAGE_MODULE_NOT_FOUND = 'Module - "%s" not found.';


    /**
     * @var ShopConfigurationDaoInterface
     */
    private $shopConfigurationDao;

    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * @var ModuleActivationServiceInterface
     */
    private $moduleActivationService;

    /**
     * @param ShopConfigurationDaoInterface    $shopConfigurationDao
     * @param ContextInterface                 $context
     * @param ModuleActivationServiceInterface $moduleActivationService
     */
    public function __construct(
        ShopConfigurationDaoInterface $shopConfigurationDao,
        ContextInterface $context,
        ModuleActivationServiceInterface $moduleActivationService
    ) {
        parent::__construct(null);
        
        $this->shopConfigurationDao = $shopConfigurationDao;
        $this->context = $context;
        $this->moduleActivationService = $moduleActivationService;
    }


    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setDescription('Activates a module.')
            ->addArgument('module-id', InputArgument::REQUIRED, 'Module ID')
            ->setHelp('Command activates module by defined module ID.');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $moduleId = $input->getArgument('module-id');
        
        if ($this->isInstalled($moduleId)) {
            $this->activateModule($output, $moduleId);
        } else {
            $output->writeLn('<error>'.sprintf(static::MESSAGE_MODULE_NOT_FOUND, $moduleId).'</error>');
        }
    }

    /**
     * @param OutputInterface $output
     * @param string          $moduleId
     */
    protected function activateModule(OutputInterface $output, string $moduleId)
    {
        try {
            $this->moduleActivationService->activate($moduleId, $this->context->getCurrentShopId());
            $output->writeLn('<info>'.sprintf(static::MESSAGE_MODULE_ACTIVATED, $moduleId).'</info>');
        } catch (ModuleSetupException $exception) {
            $output->writeLn(
                '<info>'.sprintf(static::MESSAGE_MODULE_ALREADY_ACTIVE, $moduleId).'</info>'
            );
        }
    }

    /**
     * @param string $moduleId
     * @return bool
     */
    private function isInstalled(string $moduleId): bool
    {
        $shopConfiguration = $this->shopConfigurationDao->get(
            $this->context->getCurrentShopId(),
            $this->context->getEnvironment()
        );
        
        return $shopConfiguration->hasModuleConfiguration($moduleId);
    }
}
