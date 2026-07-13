<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Migration;

use Doctrine\Migrations\Tools\Console\Command\MigrateCommand;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;

readonly class MigrationOptionsForwarder implements MigrationOptionsForwarderInterface
{
    private const EXCLUDED = ['configuration', 'db-configuration', 'em', 'conn'];

    public function mirror(InputDefinition $definition): void
    {
        foreach ((new MigrateCommand())->getDefinition()->getOptions() as $option) {
            if (!in_array($option->getName(), self::EXCLUDED, true)) {
                $definition->addOption($option);
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function collect(InputInterface $input): array
    {
        $flags = [];

        foreach ((new MigrateCommand())->getDefinition()->getOptions() as $option) {
            $name = $option->getName();
            if (!$input->hasParameterOption('--' . $name, true)) {
                continue;
            }
            $flags['--' . $name] = $option->acceptValue() ? $input->getOption($name) : true;
        }

        return $flags;
    }
}
