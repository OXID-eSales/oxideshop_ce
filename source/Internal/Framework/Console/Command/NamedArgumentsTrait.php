<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Console\Command;

use InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Trait NamedArgumentsTrait
 * @deprecated trait will be removed in v8.0
 */
trait NamedArgumentsTrait
{
    private array $requiredOptions = [];

    /**
     * @param InputOption[]  $inputOptions
     * @param InputInterface $input
     *
     * @throws InvalidArgumentException
     */
    public function checkRequiredCommandOptions(array $inputOptions, InputInterface $input): void
    {
        foreach ($this->requiredOptions as $option) {
            if (!$input->getOption($option)) {
                throw new InvalidArgumentException("The \"--{$option}\" option is required.");
            }
        }
    }

    public function setRequiredOptions(array $options): void
    {
        $this->requiredOptions = $options;
    }
}
