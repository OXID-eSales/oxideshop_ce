<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\InvalidMediaException;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\SystemProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\ProductMediaServiceInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\ProductMediaUploadProcessorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ArticlePicturesAjax extends ListComponentAjax
{
    private readonly ProductMediaUploadProcessorInterface $productMediaUploadProcessor;
    private readonly ProductMediaServiceInterface $productMediaService;

    public function __construct()
    {
        parent::__construct();
        $this->productMediaUploadProcessor = ContainerFacade::get(ProductMediaUploadProcessorInterface::class);
        $this->productMediaService = ContainerFacade::get(ProductMediaServiceInterface::class);
    }

    public function addMedia(): void
    {
        $request = Request::createFromGlobals();
        $productId = Id::fromUid($request->get('productId'));
        foreach ($request->files->get('uploadedFiles') as $uploadedFile) {
            try {
                $productMedia = $this->productMediaUploadProcessor->process($productId, $uploadedFile);
            } catch (InvalidMediaException $exception) {
                $this->sendErrorResponse($exception);
                return;
            }
            $this->productMediaService->add($productMedia);
        }
    }

    public function removeMedia(): void
    {
        $this->productMediaService
            ->remove(
                Id::fromUid(
                    Request::createFromGlobals()->get('productMediaId')
                )
            );
    }

    public function toggleMediaActiveState(): void
    {
        $productMedia = $this->getProductMedia();
        if ($productMedia->isActive()) {
            $this->productMediaService->deactivate($productMedia);
        } else {
            $this->productMediaService->activate($productMedia);
        }
    }

    public function sortMedia(): void
    {
        $this->productMediaService->sort(
            json_decode(
                Request::createFromGlobals()->get('sorting'),
                true,
                512,
                JSON_THROW_ON_ERROR
            )
        );
    }

    public function setAsThumbnail(): void
    {
        $this->productMediaService
            ->addMediaRole(
                $this->getProductMedia(),
                ProductMediaRole::from(SystemProductMediaRole::Thumb->value)
            );
    }

    public function setAsIcon(): void
    {
        $this->productMediaService
            ->addMediaRole(
                $this->getProductMedia(),
                ProductMediaRole::from(SystemProductMediaRole::Icon->value)
            );
    }

    private function getProductMedia(): ProductMedia
    {
        return $this->productMediaService
            ->get(
                Id::fromUid(
                    Request::createFromGlobals()->get('productMediaId')
                )
            );
    }

    private function sendErrorResponse(InvalidMediaException $exception): void
    {
        (new JsonResponse([
            'error' => \sprintf(
                Registry::getLang()->translateString($exception->getFormat()),
                ...$exception->getValues()
            )
        ]))
            ->send();
    }
}
