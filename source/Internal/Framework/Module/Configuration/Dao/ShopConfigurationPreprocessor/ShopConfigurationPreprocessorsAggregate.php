<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationPreprocessor;

final class ShopConfigurationPreprocessorsAggregate implements ShopConfigurationPreprocessorInterface
{
    /** @var ShopConfigurationPreprocessorInterface[] */
    private array $preprocessors;

    public function __construct(array $preprocessors)
    {
        $this->preprocessors = $preprocessors;
    }

    public function process(int $shopId, array $shopConfiguration): array
    {
        foreach ($this->preprocessors as $preprocessor) {
            $shopConfiguration = $preprocessor->process($shopId, $shopConfiguration);
        }
        return $shopConfiguration;
    }
}
