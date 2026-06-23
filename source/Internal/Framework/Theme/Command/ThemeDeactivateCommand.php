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

final class ThemeDeactivateCommand extends Command
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
        $this->setDescription('Deactivates a theme.')
            ->addArgument('theme-id', InputArgument::REQUIRED, 'Theme ID')
            ->setHelp('Command deactivates theme by defined theme ID.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $themeId = $input->getArgument('theme-id');
        $shopId = $this->context->getCurrentShopId();

        if (!$this->themeStateService->isActive($themeId, $shopId)) {
            $style->info(sprintf('Theme - "%s" is not active.', $themeId));
            return Command::SUCCESS;
        }

        $this->themeActivationBridge->deactivate($themeId, $shopId);
        $style->success(sprintf('Theme - "%s" was deactivated.', $themeId));

        return Command::SUCCESS;
    }
}
