<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Media\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Locale\DataObject\LocaleChain;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Dao\MediaAttributeDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaAttribute;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaAttributes;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

readonly class MediaAttributeService implements MediaAttributeServiceInterface
{
    public function __construct(
        private MediaAttributeDaoInterface $attributeDao,
        private ContextInterface $context,
    ) {
    }

    public function getAttributes(Media $media, string $localeCode): MediaAttributes
    {
        return $this->attributeDao->getAttributes(
            $media->getId(),
            new LocaleChain([$localeCode]),
            $this->context->getCurrentShopId()
        );
    }

    public function save(string $name, string $value, Media $media, string $localeCode): void
    {
        $this->attributeDao->save(
            new MediaAttribute(
                Id::generate(),
                $media->getId(),
                $localeCode,
                $this->context->getCurrentShopId(),
                $name,
                $value
            )
        );
    }

    public function delete(string $name, Media $media, string $localeCode): void
    {
        $this->attributeDao->delete(
            $name,
            $media->getId(),
            $localeCode,
            $this->context->getCurrentShopId()
        );
    }
}
