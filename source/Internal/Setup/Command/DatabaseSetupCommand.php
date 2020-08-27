<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Command;

use OxidEsales\EshopCommunity\Internal\Framework\Console\Command\NamedArgumentsTrait;
use OxidEsales\EshopCommunity\Internal\Setup\Database\Exception\DatabaseExistsAndNotEmptyException;
use OxidEsales\EshopCommunity\Internal\Setup\Database\Service\DatabaseCheckerInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Database\Service\DatabaseInstallerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class DatabaseSetupCommand extends Command
{
    use NamedArgumentsTrait;

    private const DB_HOST = 'db-host';
    private const DB_PORT = 'db-port';
    private const DB_NAME = 'db-name';
    private const DB_USER = 'db-user';
    private const DB_PASSWORD = 'db-password';
    private const FORCE_INSTALLATION = 'force-installation';

    /**
     * @var DatabaseCheckerInterface
     */
    private $databaseChecker;

    /**
     * @var DatabaseInstallerInterface
     */
    private $databaseInstaller;

    public function __construct(
        DatabaseCheckerInterface $databaseChecker,
        DatabaseInstallerInterface $databaseInstaller
    ) {
        parent::__construct();

        $this->databaseChecker = $databaseChecker;
        $this->databaseInstaller = $databaseInstaller;
    }

    protected function configure()
    {
        $this
            ->addOption(self::DB_HOST, null, InputOption::VALUE_REQUIRED)
            ->addOption(self::DB_PORT, null, InputOption::VALUE_REQUIRED)
            ->addOption(self::DB_NAME, null, InputOption::VALUE_REQUIRED)
            ->addOption(self::DB_USER, null, InputOption::VALUE_REQUIRED)
            ->addOption(self::DB_PASSWORD, null, InputOption::VALUE_REQUIRED)
            ->addOption(self::FORCE_INSTALLATION, null, InputOption::VALUE_NONE);
            $this->setDescription('Performs initial database setup');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->checkRequiredCommandOptions($this->getDefinition()->getOptions(), $input);

        if ($this->isActionConfirmationNeeded($input)) {
            $helper = $this->getHelper('question');
            $question = new ConfirmationQuestion($this->getQuestionText($input), false);

            if (!$helper->ask($input, $output, $question)) {
                $output->writeln('<info>Setup has been canceled.</info>');
                return Command::SUCCESS;
            }
        }

        $output->writeln('<info>Installing database...</info>');
        $this->installDatabase($input);

        $output->writeln('<info>Setup has been finished.</info>');

        return Command::SUCCESS;
    }

    /**
     * @param InputInterface $input
     * @return bool
     */
    private function doesDatabaseExist(InputInterface $input): bool
    {
        try {
            $this->databaseChecker->canCreateDatabase(
                $input->getOption(self::DB_HOST),
                (int)$input->getOption(self::DB_PORT),
                $input->getOption(self::DB_USER),
                $input->getOption(self::DB_PASSWORD),
                $input->getOption(self::DB_NAME)
            );
        } catch (DatabaseExistsAndNotEmptyException $exception) {
            return true;
        }
        return false;
    }

    /**
     * @param InputInterface $input
     * @return string
     */
    private function getQuestionText(InputInterface $input): string
    {
        return sprintf('Seems there is already OXID eShop installed in database %s. Do you want to overwrite all 
        existing data and install it anyway? [y/N] ', $input->getOption(self::DB_NAME));
    }

    /**
     * @param InputInterface $input
     * @return bool
     */
    private function forceDatabaseInstallation(InputInterface $input): bool
    {
        $value = $input->getOption(self::FORCE_INSTALLATION);
        return (isset($value) && $value);
    }

    /**
     * @param InputInterface $input
     * @return bool
     */
    private function isActionConfirmationNeeded(InputInterface $input): bool
    {
        return (!$this->forceDatabaseInstallation($input) && $this->doesDatabaseExist($input));
    }

    /**
     * @param InputInterface $input
     */
    private function installDatabase(InputInterface $input): void
    {
        $this->databaseInstaller->install(
            $input->getOption(self::DB_HOST),
            (int) $input->getOption(self::DB_PORT),
            $input->getOption(self::DB_USER),
            $input->getOption(self::DB_PASSWORD),
            $input->getOption(self::DB_NAME)
        );
    }
}
