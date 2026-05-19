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
use Symfony\Component\Console\Style\SymfonyStyle;

class ModuleActivateCommand extends Command
{
    /** @deprecated */
    public const MESSAGE_MODULE_ACTIVATED = 'Module - "%s" was activated.';
    /** @deprecated */
    public const MESSAGE_MODULE_NOT_FOUND = 'Module - "%s" not found.';

    public function __construct(
        private ModuleConfigurationDaoInterface $moduleConfigurationDao,
        private ContextInterface $context,
        private ModuleActivationServiceInterface $moduleActivationService
    ) {
        parent::__construct();
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
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $style = new SymfonyStyle($input, $output);
        $moduleId = $input->getArgument('module-id');

        if (!$this->isInstalled($moduleId)) {
            $style->error(sprintf(static::MESSAGE_MODULE_NOT_FOUND, $moduleId));
            return Command::FAILURE;
        }

        $this->activateModule($style, $moduleId);

        return Command::SUCCESS;
    }

    /**
     * @param SymfonyStyle $style
     * @param string       $moduleId
     *
     * @return void
     */
    protected function activateModule(SymfonyStyle $style, string $moduleId)
    {
        $this->moduleActivationService->activate($moduleId, $this->context->getCurrentShopId());
        $style->success(sprintf(static::MESSAGE_MODULE_ACTIVATED, $moduleId));
    }

    private function isInstalled(string $moduleId): bool
    {
        return $this->moduleConfigurationDao->exists($moduleId, $this->context->getCurrentShopId());
    }
}
