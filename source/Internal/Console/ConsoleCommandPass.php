<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Console;

use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Registers tagged services.
 * @internal
 */
class ConsoleCommandPass implements CompilerPassInterface
{
    const COMMANDS_PARAMETER_NAME = 'console.command.ids';

    const COMMAND_TAG = 'console.command';

    /**
     * @param ContainerBuilder $container
     * @throws \ReflectionException
     */
    public function process(ContainerBuilder $container)
    {
        $commandServices = $container->findTaggedServiceIds(static::COMMAND_TAG, true);
        $serviceIds = [];

        foreach ($commandServices as $id => $tags) {
            $serviceIds[] = $id;
            $definition = $container->getDefinition($id);
            $class = $container->getParameterBag()->resolveValue($definition->getClass());
            if (isset($tags[0]['command'])) {
                $commandName = $tags[0]['command'];
            } else {
                if (!$r = $container->getReflectionClass($class)) {
                    throw new InvalidArgumentException(sprintf('Class "%s" used for service "%s" cannot be found.', $class, $id));
                }
                $commandName = $class::getDefaultName();
            }
            $definition->addMethodCall('setName', array($commandName));
        }

        $container->setParameter(static::COMMANDS_PARAMETER_NAME, $serviceIds);
    }
}
