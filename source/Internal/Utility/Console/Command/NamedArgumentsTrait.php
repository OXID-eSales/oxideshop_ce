<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Utility\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Trait NamedArgumentsTrait
 * Turns Symfony command options with VALUE_REQUIRED into "named arguments"
 * @see https://github.com/symfony/symfony/issues/14716
 */
trait NamedArgumentsTrait
{
    /**
     * @param InputOption[] $inputOptions
     * @param InputInterface $input
     */
    public function validateRequiredOptions(array $inputOptions, InputInterface $input): void
    {
        foreach ($inputOptions as $option) {
            $name  = $option->getName();
            $value = $input->getOption($name);
            if (!isset($value) && $option->isValueRequired()) {
                throw new \InvalidArgumentException("The \"--$name\" option is required.");
            }
        }
    }
}
