<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Command;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Cache\ModuleCacheServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Cache\ShopTemplateCacheServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ThemeActivateCommand extends Command
{
    private const MESSAGE_THEME_IS_ACTIVE = 'Theme - "%s" is already active.';
    private const MESSAGE_THEME_ACTIVATED = 'Theme - "%s" was activated.';
    private const MESSAGE_THEME_NOT_FOUND = 'Theme - "%s" not found.';

    public function __construct(
        private readonly ShopAdapterInterface $shopAdapter,
        private readonly ShopTemplateCacheServiceInterface $shopTemplateCacheService,
        private readonly ModuleCacheServiceInterface $moduleCacheService
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
        $themeId = $input->getArgument('theme-id');

        if (!$this->shopAdapter->themeExists($themeId)) {
            $output->writeLn(
                '<error>' . sprintf(self::MESSAGE_THEME_NOT_FOUND, $themeId) . '</error>'
            );
            return Command::INVALID;
        }

        if ($this->shopAdapter->getActiveThemeId() === $themeId) {
            $output->writeln(
                '<comment>' . sprintf(self::MESSAGE_THEME_IS_ACTIVE, $themeId) . '</comment>'
            );
            return Command::SUCCESS;
        }

        $this->shopAdapter->activateTheme($themeId);
        $this->moduleCacheService->invalidateAll();
        $this->shopTemplateCacheService->invalidateAllShopsCache();
        $output->writeLn('<info>' . sprintf(self::MESSAGE_THEME_ACTIVATED, $themeId) . '</info>');

        return Command::SUCCESS;
    }
}
