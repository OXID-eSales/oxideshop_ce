<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Command;

use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\Eshop\Core\Module\ModuleInstaller;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Adapter\ShopAdapterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command deactivates module by module id.
 * @internal
 */
class ModuleDeactivateCommand extends Command
{
    const MESSAGE_MODULE_DEACTIVATED = 'Module - "%s" has been deactivated.';

    const MESSAGE_NOT_POSSIBLE_TO_DEACTIVATE = 'It was not possible to deactivate module - "%s", maybe it was not active?';

    const MESSAGE_MODULE_NOT_FOUND = 'Module - "%s" not found.';

    const ARGUMENT_MODULE_ID = 'module-id';

    /**
     * @var ShopAdapterInterface
     */
    private $shopAdapter;

    /**
     * @param ShopAdapterInterface $shopAdapter
     */
    public function __construct(ShopAdapterInterface $shopAdapter)
    {
        parent::__construct(null);
        $this->shopAdapter = $shopAdapter;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setDescription('Deactivates a module.')
            ->addArgument(static::ARGUMENT_MODULE_ID, InputArgument::REQUIRED, 'Module ID')
            ->setHelp('Command deactivates module by defined module ID.');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $moduleId = $input->getArgument('module-id');
        $modules = $this->shopAdapter->getModules();

        if (isset($modules[$moduleId])) {
            $this->deactivateModule($output, $modules, $moduleId);
        } else {
            $output->writeLn('<error>'.sprintf(static::MESSAGE_MODULE_NOT_FOUND, $moduleId).'</error>');
        }
    }

    /**
     * @param OutputInterface $output
     * @param Module[]        $modules
     * @param string          $moduleId
     */
    protected function deactivateModule(OutputInterface $output, array $modules, string $moduleId)
    {
        /** @var ModuleInstaller $moduleInstaller */
        $moduleInstaller = Registry::get(ModuleInstaller::class);
        $module = $modules[$moduleId];
        if ($module->isActive() && $moduleInstaller->deactivate($module)) {
            $output->writeLn('<info>' . sprintf(static::MESSAGE_MODULE_DEACTIVATED, $moduleId) . '</info>');
        } else {
            $output->writeLn('<info>' . sprintf(static::MESSAGE_NOT_POSSIBLE_TO_DEACTIVATE, $moduleId) . '</info>');
        }
    }
}
