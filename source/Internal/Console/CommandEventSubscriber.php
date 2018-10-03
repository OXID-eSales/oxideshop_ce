<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Console;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Commands event subscriber class which executes some necessary actions before executing commands.
 * @internal
 */
class CommandEventSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            ConsoleEvents::COMMAND => 'registerShopIdAsGlobalOption',
        ];
    }

    /**
     * Registers option: --shop-id
     *
     * @param ConsoleCommandEvent $event
     *
     * @return ConsoleCommandEvent
     */
    public function registerShopIdAsGlobalOption(ConsoleCommandEvent $event)
    {
        $definition = $event->getCommand()->getDefinition();
        $option = new InputOption(
            'shop-id',
            null,
            InputOption::VALUE_OPTIONAL,
            'Defines on which subshop commands should be executed',
            1
        );
        $definition->addOption($option);
        $input = $event->getInput();
        $input->bind($definition);

        $definition = $event->getCommand()->getApplication()->getDefinition();
        $definition->addOption($option);

        return $event;
    }
}
