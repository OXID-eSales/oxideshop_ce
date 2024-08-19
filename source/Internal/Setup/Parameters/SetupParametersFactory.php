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

readonly class SetupParametersFactory implements SetupParametersFactoryInterface
{
    public function __construct(
        private BasicContextInterface $basicContext
    ) {
    }

    public function create(DefaultLanguage $language): SetupParameters
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
        $setupParameters->setLanguage($language);

        return $setupParameters;
    }
}
