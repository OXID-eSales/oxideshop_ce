<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController;
use OxidEsales\EshopCommunity\Internal\Domain\Locale\DataObject\Locale;
use OxidEsales\EshopCommunity\Internal\Domain\Locale\Service\LocaleServiceInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Media\MediaUrlGeneratorInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Service\MediaAttributeServiceInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service\ProductMediaServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Symfony\Component\HttpFoundation\Request;

class ArticleImageAlt extends AdminDetailsController
{
    public function __construct(
        private readonly ProductMediaServiceInterface $productMediaService,
        private readonly MediaAttributeServiceInterface $attributeService,
        private readonly LocaleServiceInterface $localeService,
        private readonly MediaUrlGeneratorInterface $mediaUrlGenerator,
        private readonly ContextInterface $context,
        private readonly Request $request,
        private readonly string $thumbnailSize,
    ) {
        parent::__construct();
    }

    public function render(): string
    {
        parent::render();

        $productId = Id::fromString($this->getEditObjectId());
        $shopId = $this->context->getCurrentShopId();
        $locales = $this->localeService->getForShop($shopId);

        $images = [];
        foreach ($this->productMediaService->getByProduct($productId) as $media) {
            $images[] = $this->buildImageData($media, $locales);
        }

        $this->_aViewData['allImages'] = $images;
        $this->_aViewData['availableLocales'] = array_map(
            fn(Locale $locale): array => ['id' => $locale->getCode(), 'name' => $locale->getName()],
            $locales
        );

        return 'article_imagealt';
    }

    public function save(): void
    {
        $productMediaId = (string) $this->request->request->get('productMediaId', '');
        $productMedia = $this->productMediaService->get(Id::fromString($productMediaId));
        if ((string) $productMedia->getProductId() !== $this->getEditObjectId()) {
            return;
        }

        $media = $productMedia->getMedia();
        foreach ($this->getSubmittedAltTexts($productMediaId) as $localeCode => $altText) {
            $altText !== ''
                ? $this->attributeService->save('alt', $altText, $media, $localeCode)
                : $this->attributeService->delete('alt', $media, $localeCode);
        }
    }

    /** @return array<string, string> */
    private function getSubmittedAltTexts(string $productMediaId): array
    {
        $altTexts = $this->request->request->all('altTexts')[$productMediaId] ?? [];

        return array_map(static fn($altText) => trim((string) $altText), $altTexts);
    }

    /** @param Locale[] $locales */
    private function buildImageData(ProductMedia $productMedia, array $locales): array
    {
        $media = $productMedia->getMedia();

        $altTexts = [];
        foreach ($locales as $locale) {
            $attributes = $this->attributeService->getAttributes($media, $locale->getCode());
            $altTexts[$locale->getCode()] = $attributes->has('alt') ? $attributes->getAlt() : '';
        }

        return [
            'productMediaId' => (string) $productMedia->getId(),
            'filename' => basename((string) $media->getMediaPath()),
            'roles' => array_map(
                fn(ProductMediaRole $role): string => $role->value(),
                $productMedia->getRoleSet()->getRoles()->toArray()
            ),
            'position' => $productMedia->hasPosition() ? $productMedia->getPosition() : 1,
            'url' => $this->mediaUrlGenerator->generateSizedImageUrl($media, $this->thumbnailSize),
            'altTexts' => $altTexts,
        ];
    }
}
