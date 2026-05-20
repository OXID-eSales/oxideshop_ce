<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Media\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Locale\Service\LocaleChainResolverInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Dao\MediaAttributeDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaAttributes;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

readonly class MediaAttributeViewService implements MediaAttributeViewServiceInterface
{
    public function __construct(
        private MediaAttributeDaoInterface $attributeDao,
        private ContextInterface $context,
        private LocaleChainResolverInterface $localeChainResolver,
    ) {
    }

    public function getAttributes(Media $media, string $localeCode): MediaAttributes
    {
        return $this->attributeDao->getAttributes(
            $media->getId(),
            $this->localeChainResolver->getActiveFallbackChain($localeCode),
            $this->context->getCurrentShopId()
        );
    }
}
