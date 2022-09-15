<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Command;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ModuleDeactivateCommand extends Command
{
    public const MESSAGE_MODULE_DEACTIVATED = 'Module - "%s" has been deactivated.';
    public const MESSAGE_MODULE_NOT_FOUND = 'Module - "%s" not found.';
    private const ARGUMENT_MODULE_ID = 'module-id';

    public function __construct(
        private ModuleConfigurationDaoInterface $moduleConfigurationDao,
        private ContextInterface $context,
        private ModuleActivationServiceInterface $moduleActivationService
    ) {
        parent::__construct(null);
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setDescription('Deactivates a module.')
            ->addArgument(static::ARGUMENT_MODULE_ID, InputArgument::REQUIRED, 'Module ID')
            ->setHelp('Command deactivates module by defined module ID.');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
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

    /**
     * @param OutputInterface $output
     * @param string          $moduleId
     */
    protected function deactivateModule(OutputInterface $output, string $moduleId)
    {
        $this->moduleActivationService->deactivate($moduleId, $this->context->getCurrentShopId());
        $output->writeLn(
            '<info>' . sprintf(static::MESSAGE_MODULE_DEACTIVATED, $moduleId) . '</info>'
        );
    }

    /**
     * @param string $moduleId
     * @return bool
     */
    private function isInstalled(string $moduleId): bool
    {
        return $this->moduleConfigurationDao->exists($moduleId, $this->context->getCurrentShopId());
    }
}
