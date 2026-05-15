<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Command;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ModuleConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ModuleActivateCommand extends Command
{
    public function __construct(
        private readonly ModuleActivationServiceInterface $moduleActivationService,
        private readonly ContextInterface $context,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Activates a module.')
            ->addArgument('module-id', InputArgument::REQUIRED, 'Module ID')
            ->setHelp('Command activates module by defined module ID.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $moduleId = $input->getArgument('module-id');

        try {
            $this->moduleActivationService->activate($moduleId, $this->context->getCurrentShopId());
        } catch (ModuleConfigurationNotFoundException) {
            $style->error(sprintf('Module - "%s" not found.', $moduleId));
            return Command::FAILURE;
        }

        $style->success(sprintf('Module - "%s" was activated.', $moduleId));
        return Command::SUCCESS;
    }
}