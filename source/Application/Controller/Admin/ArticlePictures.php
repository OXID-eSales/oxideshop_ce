<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Domain\Media\MediaUrlGeneratorInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Dao\ProductMediaDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;

class ArticlePictures extends AdminDetailsController
{
    private string $detailImageSize;
    private string $zoomImageSize;
    private MediaUrlGeneratorInterface $mediaUrlGenerator;
    private ProductMediaDaoInterface $productMediaDao;

    public function __construct()
    {
        $this->mediaUrlGenerator = ContainerFacade::get(MediaUrlGeneratorInterface::class);
        $this->productMediaDao = ContainerFacade::get(ProductMediaDaoInterface::class);

        $this->detailImageSize = ContainerFacade::getParameter('oxid_esales.theme.admin.media.image_grid_size');
        $this->zoomImageSize = ContainerFacade::getParameter('oxid_esales.theme.admin.media.image_zoom_size');

        parent::__construct();
    }

    public function render()
    {
        parent::render();

        $productId = Id::fromString($this->getEditObjectId());

        $icon = $this->productMediaDao->getByRole($productId, ProductMediaRole::from(ProductMediaRole::ICON));
        $thumbnail = $this->productMediaDao->getByRole($productId, ProductMediaRole::from(ProductMediaRole::THUMBNAIL));

        $this->_aViewData['productImages'] = [
            'icon' => $icon ? $this->buildImageData($icon) : null,
            'thumbnail' => $thumbnail ? $this->buildImageData($thumbnail) : null,
            'detailImages' => $this->productMediaDao
                ->getAllByRole($productId, ProductMediaRole::from(ProductMediaRole::DETAIL))
                ->map(fn(ProductMedia $media) => $this->buildImageData($media))
                ->toArray(),
        ];

        return 'article_pictures';
    }

    private function buildImageData(ProductMedia $media): array
    {
        return [
            'id' => (string) $media->getId(),
            'productId' => (string) $media->getProductId(),
            'url' => $this->mediaUrlGenerator->generateSizedImageUrl($media->getMedia(), $this->detailImageSize),
            'zoomUrl' => $this->mediaUrlGenerator->generateSizedImageUrl($media->getMedia(), $this->zoomImageSize),
            'position' => $media->getPosition(),
            'active' => $media->isActive(),
        ];
    }
}
