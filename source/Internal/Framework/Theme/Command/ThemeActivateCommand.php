<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Command;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Bridge\ThemeActivationBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ThemeStateServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ThemeActivateCommand extends Command
{
    public function __construct(
        private readonly ThemeActivationBridgeInterface $themeActivationBridge,
        private readonly ThemeStateServiceInterface $themeStateService,
        private readonly ThemeConfigurationDaoInterface $themeConfigurationDao,
        private readonly ContextInterface $context,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Activates a theme.')
            ->addArgument('theme-id', InputArgument::REQUIRED, 'Theme ID')
            ->setHelp('Command activates theme by defined theme ID.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $themeId = $input->getArgument('theme-id');
        $shopId = $this->context->getCurrentShopId();

        if (!$this->themeConfigurationDao->exists($themeId, $shopId)) {
            $style->error(sprintf('Theme - "%s" not found.', $themeId));
            return Command::FAILURE;
        }

        if ($this->themeStateService->getActiveThemeId($shopId) === $themeId) {
            $style->info(sprintf('Theme - "%s" is already active.', $themeId));
            return Command::SUCCESS;
        }

        $this->themeActivationBridge->activate($themeId, $shopId);
        $style->success(sprintf('Theme - "%s" was activated.', $themeId));

        return Command::SUCCESS;
    }
}
