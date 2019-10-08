<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Command;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\ModuleSetupException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command deactivates module by module id.
 */
class ModuleDeactivateCommand extends Command
{
    const MESSAGE_MODULE_DEACTIVATED = 'Module - "%s" has been deactivated.';

    const MESSAGE_NOT_POSSIBLE_TO_DEACTIVATE =
        'It was not possible to deactivate module - "%s", maybe it was not active?';

    const MESSAGE_MODULE_NOT_FOUND = 'Module - "%s" not found.';

    const ARGUMENT_MODULE_ID = 'module-id';

    /**
     * @var ShopConfigurationDaoInterface
     */
    private $shopConfigurationDao;

    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * @var ModuleActivationServiceInterface
     */
    private $moduleActivationService;

    /**
     * @param ShopConfigurationDaoInterface    $shopConfigurationDao
     * @param ContextInterface                 $context
     * @param ModuleActivationServiceInterface $moduleActivationService
     */
    public function __construct(
        ShopConfigurationDaoInterface $shopConfigurationDao,
        ContextInterface $context,
        ModuleActivationServiceInterface $moduleActivationService
    ) {
        parent::__construct(null);

        $this->shopConfigurationDao = $shopConfigurationDao;
        $this->context = $context;
        $this->moduleActivationService = $moduleActivationService;
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

        if ($this->isInstalled($moduleId)) {
            $this->deactivateModule($output, $moduleId);
        } else {
            $output->writeLn('<error>'.sprintf(static::MESSAGE_MODULE_NOT_FOUND, $moduleId).'</error>');
        }
    }

    /**
     * @param OutputInterface $output
     * @param string          $moduleId
     */
    protected function deactivateModule(OutputInterface $output, string $moduleId)
    {
        try {
            $this->moduleActivationService->deactivate($moduleId, $this->context->getCurrentShopId());
            $output->writeLn(
                '<info>' . sprintf(static::MESSAGE_MODULE_DEACTIVATED, $moduleId) . '</info>'
            );
        } catch (ModuleSetupException $exception) {
            $output->writeLn(
                '<info>' . sprintf(static::MESSAGE_NOT_POSSIBLE_TO_DEACTIVATE, $moduleId) . '</info>'
            );
        }
    }

    /**
     * @param string $moduleId
     * @return bool
     */
    private function isInstalled(string $moduleId): bool
    {
        $shopConfiguration = $this
            ->shopConfigurationDao
            ->get(
                $this->context->getCurrentShopId()
            );

        return $shopConfiguration->hasModuleConfiguration($moduleId);
    }
}
