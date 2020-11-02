<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Command;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\ModuleSetupException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command deactivates module by module id.
 */
class ModuleDeactivateCommand extends Command
{
    public const MESSAGE_MODULE_DEACTIVATED = 'Module - "%s" has been deactivated.';
    public const MESSAGE_NOT_POSSIBLE_TO_DEACTIVATE =
        'It was not possible to deactivate module - "%s", maybe it was not active?';
    public const MESSAGE_MODULE_NOT_FOUND = 'Module - "%s" not found.';
    private const ARGUMENT_MODULE_ID = 'module-id';

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
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setDescription('Deactivates a module.')
            ->addArgument(static::ARGUMENT_MODULE_ID, InputArgument::REQUIRED, 'Module ID')
            ->setHelp('Command deactivates module by defined module ID.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $moduleId = $input->getArgument('module-id');

        if ($this->isInstalled($moduleId)) {
            $this->deactivateModule($output, $moduleId);
        } else {
            $output->writeLn('<error>' . sprintf(static::MESSAGE_MODULE_NOT_FOUND, $moduleId) . '</error>');
        }

        return 0;
    }

    protected function deactivateModule(OutputInterface $output, string $moduleId): void
    {
        try {
            $this->moduleActivationService->deactivate($moduleId, $this->context->getCurrentShopId());
            $output->writeLn(
                '<info>' . sprintf(static::MESSAGE_MODULE_DEACTIVATED, $moduleId) . '</info>'
            );
        } catch (ModuleSetupException $exception) {
            $output->writeLn(
                '<info>' . sprintf(static::MESSAGE_NOT_POSSIBLE_TO_DEACTIVATE, $moduleId) . '</info>'
            );
        }
    }

    private function isInstalled(string $moduleId): bool
    {
        $shopConfiguration = $this
            ->shopConfigurationDao
            ->get(
                $this->context->getCurrentShopId()
            );

        return $shopConfiguration->hasModuleConfiguration($moduleId);
    }
}
