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
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Utility\Email\EmailValidatorServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUserCommand extends Command
{
    private const ADMIN_EMAIL = 'admin-email';
    private const ADMIN_PASSWORD = 'admin-password';

    public function __construct(
        private readonly EmailValidatorServiceInterface $emailValidatorService,
        private readonly AdminUserServiceInterface $adminService,
        private readonly BasicContextInterface $basicContext
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(self::ADMIN_EMAIL, InputArgument::REQUIRED)
            ->addArgument(self::ADMIN_PASSWORD, InputArgument::REQUIRED);
        $this->setDescription('Creates admin user');
    }

    /**
     * @throws InvalidEmailException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->validateAdminEmail($input->getArgument(self::ADMIN_EMAIL));

        $output->writeln('<info>Creating administrator account...</info>');
        $this->createAdmin($input);

        $output->writeln('<info>Administrator account has been created.</info>');

        return Command::SUCCESS;
    }

    /**
     * @throws InvalidEmailException
     */
    private function validateAdminEmail(string $email): void
    {
        if (!$this->emailValidatorService->isEmailValid($email)) {
            throw new InvalidEmailException($email);
        }
    }

    private function createAdmin(InputInterface $input): void
    {
        $this->adminService->createAdmin(
            $input->getArgument(self::ADMIN_EMAIL),
            $input->getArgument(self::ADMIN_PASSWORD),
            Admin::MALL_ADMIN,
            $this->basicContext->getDefaultShopId()
        );
    }
}
