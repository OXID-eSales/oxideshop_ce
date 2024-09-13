<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup;

use OxidEsales\EshopCommunity\Internal\Setup\Database\ShopDbManagerInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Htaccess\HtaccessUpdaterInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Language\LanguageInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Parameters\SetupParametersFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Setup\ShopConfiguration\ShopConfigurationUpdaterInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Validator\SetupInfrastructureValidatorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ShopSetupCommand extends Command
{
    private const DEFAULT_LANG = 'en';

    public function __construct(
        private readonly SetupParametersFactoryInterface $setupParametersFactory,
        private readonly SetupInfrastructureValidatorInterface $setupInfrastructureValidator,
        private readonly ShopDbManagerInterface $shopDbManager,
        private readonly LanguageInstallerInterface $languageInstaller,
        private readonly HtaccessUpdaterInterface $htaccessUpdateService,
        private readonly ShopConfigurationUpdaterInterface $shopConfigurationUpdater,
        private readonly string $optionSetupLanguage,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption(
            name: $this->optionSetupLanguage,
            mode: InputOption::VALUE_OPTIONAL,
            default: self::DEFAULT_LANG
        );
        $this->setDescription('Performs initial shop setup');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Running pre-setup checks...</info>');
        $setupParameters = $this->setupParametersFactory
            ->create($input);
        $this->setupInfrastructureValidator
            ->validate($setupParameters);

        $output->writeln('<info>Updating htaccess file...</info>');
        $this->htaccessUpdateService
            ->updateRewriteBaseDirective(
                $setupParameters->getShopBaseUrl()
            );

        $output->writeln('<info>Installing database...</info>');
        $this->shopDbManager
            ->create(
                $setupParameters->getDbConfig()
            );

        $output->writeln('<info>Installing language...</info>');
        $this->languageInstaller
            ->install(
                $setupParameters->getLanguage()
            );

        $this->shopConfigurationUpdater
            ->saveShopSetupTime();

        $output->writeln('<info>Setup has been finished.</info>');

        return 0;
    }
}
