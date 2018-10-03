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
 * Command activates module by module id.
 * @internal
 */
class ModuleActivateCommand extends Command
{
    const MESSAGE_MODULE_ALREADY_ACTIVE = 'Module - "%s" already active.';

    const MESSAGE_MODULE_ACTIVATED = 'Module - "%s" was activated.';

    const MESSAGE_MODULE_NOT_FOUND = 'Module - "%s" not found.';


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
        $this->setDescription('Activates a module.')
            ->addArgument('module-id', InputArgument::REQUIRED, 'Module ID')
            ->setHelp('Command activates module by defined module ID.');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $moduleId = $input->getArgument('module-id');
        $modules = $this->shopAdapter->getModules();

        /** @var Module $module */
        if (isset($modules[$moduleId])) {
            $this->activateModule($output, $modules[$moduleId]);
        } else {
            $output->writeLn('<error>'.sprintf(static::MESSAGE_MODULE_NOT_FOUND, $moduleId).'</error>');
        }
    }

    /**
     * @param OutputInterface $output
     * @param Module          $module
     */
    protected function activateModule(OutputInterface $output, Module $module)
    {
        /** @var ModuleInstaller $moduleInstaller */
        $moduleInstaller = Registry::get(ModuleInstaller::class);

        if ($module->isActive()) {
            $output->writeLn('<info>'.sprintf(static::MESSAGE_MODULE_ALREADY_ACTIVE, $module->getId()).'</info>');
        } else {
            $moduleInstaller->activate($module);
            $output->writeLn('<info>'.sprintf(static::MESSAGE_MODULE_ACTIVATED, $module->getId()).'</info>');
        }
    }
}
