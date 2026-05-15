<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup;

use OxidEsales\EshopCommunity\Internal\Setup\Database\ShopDbManagerInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Htaccess\HtaccessUpdaterInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Language\DefaultLanguage;
use OxidEsales\EshopCommunity\Internal\Setup\Language\LanguageInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Parameters\SetupParametersFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Setup\ShopConfiguration\ShopConfigurationUpdaterInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Validator\SetupInfrastructureValidatorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ShopSetupCommand extends Command
{
    private const DEFAULT_LANG = 'en';

    public function __construct(
        private readonly SetupParametersFactoryInterface $setupParametersFactory,
        private readonly SetupInfrastructureValidatorInterface $setupInfrastructureValidator,
        private readonly ShopDbManagerInterface $shopDbManager,
        private readonly LanguageInstallerInterface $languageInstaller,
        private readonly HtaccessUpdaterInterface $htaccessUpdateService,
        private readonly ShopConfigurationUpdaterInterface $shopConfigurationUpdater
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption(
            name: 'language',
            mode: InputOption::VALUE_OPTIONAL,
            default: self::DEFAULT_LANG
        );
        $this->setDescription('Performs initial shop setup');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);

        $style->text('Running pre-setup checks...');
        $setupParameters = $this->setupParametersFactory
            ->create(new DefaultLanguage($input->getOption('language')));
        $this->setupInfrastructureValidator
            ->validate($setupParameters);

        $style->text('Updating htaccess file...');
        $this->htaccessUpdateService
            ->updateRewriteBaseDirective(
                $setupParameters->getShopBaseUrl()
            );

        $style->text('Installing database...');
        $this->shopDbManager
            ->create(
                $setupParameters->getDbConfig()
            );

        $style->text('Installing language...');
        $this->languageInstaller
            ->install(
                $setupParameters->getLanguage()
            );

        $this->shopConfigurationUpdater
            ->saveShopSetupTime();

        $style->success('Setup has been finished.');

        return Command::SUCCESS;
    }
}
