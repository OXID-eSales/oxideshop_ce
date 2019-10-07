<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Responsible for executing commands.
 */
interface ExecutorInterface
{
    /**
     * @param InputInterface|null  $input
     * @param OutputInterface|null $output
     */
    public function execute(InputInterface $input = null, OutputInterface $output = null);
}
