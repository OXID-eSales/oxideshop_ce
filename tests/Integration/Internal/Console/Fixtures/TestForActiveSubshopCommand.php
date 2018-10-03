<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Console\Fixtures;

use OxidEsales\Eshop\Core\Registry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestForActiveSubshopCommand extends Command
{
    protected function configure()
    {
        $this->setName('oe:tests:get-subshop');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Active shop ' . Registry::getSession()->getVariable('shp'));
    }
}
