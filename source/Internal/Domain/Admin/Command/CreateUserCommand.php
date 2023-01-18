<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Admin\Command;

use OxidEsales\EshopCommunity\Internal\Domain\Admin\DataObject\Admin;
use OxidEsales\EshopCommunity\Internal\Domain\Admin\Exception\InvalidEmailException;
use OxidEsales\EshopCommunity\Internal\Domain\Admin\Service\AdminUserServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Console\Command\NamedArgumentsTrait;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Utility\Email\EmailValidatorServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUserCommand extends Command
{
    use NamedArgumentsTrait;

    private const ADMIN_EMAIL = 'admin-email';
    private const ADMIN_PASSWORD = 'admin-password';

    public function __construct(
        private EmailValidatorServiceInterface $emailValidatorService,
        private AdminUserServiceInterface $adminService,
        private BasicContextInterface $basicContext
    ) {
        parent::__construct();
    }

    /**
     * Configures the current command.
     */
    protected function configure(): void
    {
        $this
            ->addOption(self::ADMIN_EMAIL, null, InputOption::VALUE_REQUIRED)
            ->addOption(self::ADMIN_PASSWORD, null, InputOption::VALUE_REQUIRED);
        $this->setDescription('Creates admin user');

        $this->setRequiredOptions([
            self::ADMIN_EMAIL,
            self::ADMIN_PASSWORD,
        ]);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws InvalidEmailException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->checkRequiredCommandOptions($this->getDefinition()->getOptions(), $input);

        $this->validateAdminEmail($input->getOption(self::ADMIN_EMAIL));

        $output->writeln('<info>Creating administrator account...</info>');
        $this->createAdmin($input);

        $output->writeln('<info>Administrator account has been created.</info>');

        return Command::SUCCESS;
    }

    /**
     * @param string $email
     *
     * @throws InvalidEmailException
     */
    private function validateAdminEmail(string $email): void
    {
        if (!$this->emailValidatorService->isEmailValid($email)) {
            throw new InvalidEmailException($email);
        }
    }

    /**
     * @param InputInterface $input
     */
    private function createAdmin(InputInterface $input): void
    {
        $this->adminService->createAdmin(
            $input->getOption(self::ADMIN_EMAIL),
            $input->getOption(self::ADMIN_PASSWORD),
            Admin::MALL_ADMIN,
            $this->basicContext->getDefaultShopId()
        );
    }
}
