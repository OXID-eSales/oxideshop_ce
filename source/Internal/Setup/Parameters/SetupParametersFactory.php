<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Parameters;

use OxidEsales\EshopCommunity\Internal\Framework\Configuration\DataObject\DatabaseConfiguration;
use OxidEsales\EshopCommunity\Internal\Setup\Htaccess\ShopBaseUrl;
use OxidEsales\EshopCommunity\Internal\Setup\Language\DefaultLanguage;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Console\Input\InputInterface;

class SetupParametersFactory implements SetupParametersFactoryInterface
{
    public function __construct(
        private readonly BasicContextInterface $basicContext,
        private readonly string $optionSetupLanguage,
    ) {
    }

    public function create(InputInterface $input): SetupParameters
    {
        $setupParameters = new SetupParameters();

        $setupParameters->setCacheDir(
            $this->basicContext->getCacheDirectory()
        );
        $setupParameters->setDbConfig(
            new DatabaseConfiguration(
                $this->basicContext->getDatabaseUrl()
            )
        );
        $setupParameters->setShopBaseUrl(
            new ShopBaseUrl(
                $this->basicContext->getShopBaseUrl()
            )
        );
        $setupParameters->setLanguage(
            new DefaultLanguage(
                $input->getOption($this->optionSetupLanguage)
            )
        );

        return $setupParameters;
    }
}
