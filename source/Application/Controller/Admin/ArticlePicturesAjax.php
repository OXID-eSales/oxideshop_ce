<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\Exception\MediaValidationException;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\Exception\FileExtensionMismatchException;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\Exception\FileSizeTooLargeException;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\Exception\FileSizeTooSmallException;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\Exception\MimeBaseTypeMismatchException;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\Exception\MimeGuessMismatchException;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\Exception\MimeTypeGuessFailedException;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\Exception\UploadInvalidException;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service\ProductMediaServiceInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service\ProductMediaUploadProcessorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ArticlePicturesAjax extends ListComponentAjax
{
    private readonly ProductMediaUploadProcessorInterface $productMediaUploadProcessor;
    private readonly ProductMediaServiceInterface $productMediaService;
    private readonly InputBag $requestData;
    private readonly FileBag $requestFiles;

    public function __construct()
    {
        parent::__construct();
        $this->productMediaUploadProcessor = ContainerFacade::get(ProductMediaUploadProcessorInterface::class);
        $this->productMediaService = ContainerFacade::get(ProductMediaServiceInterface::class);
        $this->requestData = ContainerFacade::get(Request::class)->request;
        $this->requestFiles = ContainerFacade::get(Request::class)->files;
    }

    public function addMedia(): void
    {
        $errors = [];
        $productId = Id::fromUid($this->requestData->getString('productId'));
        $role = ProductMediaRole::from($this->requestData->getString('role'));
        foreach ($this->requestFiles->get('uploadedFiles') as $uploadedFile) {
            try {
                $productMedia = $this->productMediaUploadProcessor->process($productId, $uploadedFile);
            } catch (MediaValidationException $e) {
                $errors[] = $this->formatErrorWithFilename($e, $uploadedFile->getClientOriginalName());
                continue;
            }
            $productMedia->getRoleSet()->addRole($role);
            $this->productMediaService->add($productMedia);
        }
        if ($errors !== []) {
            $this->sendErrorsResponse($errors);
        }
    }

    public function replaceMedia(): void
    {
        $this->addMedia();
        $this->removeMedia();
    }

    public function removeMedia(): void
    {
        $this->productMediaService
            ->remove(
                Id::fromUid($this->requestData->getString('productMediaId'))
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
                $this->requestData->getString('sorting'),
                true,
                512,
                JSON_THROW_ON_ERROR
            )
        );
    }

    private function getProductMedia(): ProductMedia
    {
        return $this->productMediaService
            ->get(
                Id::fromUid($this->requestData->getString('productMediaId'))
            );
    }

    private function mapExceptionToTranslation(\Throwable $e): array
    {
        return match (true) {
            $e instanceof FileSizeTooSmallException =>
                ['ERR_MEDIA_SIZE_TOO_SMALL', [$e->getActualFormatted(), $e->getMinFormatted()]],

            $e instanceof FileSizeTooLargeException =>
                ['ERR_MEDIA_SIZE_TOO_LARGE', [$e->getActualFormatted(), $e->getMaxFormatted()]],

            $e instanceof MimeBaseTypeMismatchException =>
                ['ERR_MEDIA_MIME_BASETYPE_MISMATCH', [$e->getGuessedMime(), $e->getRequiredBasePrefix()]],

            $e instanceof MimeGuessMismatchException =>
                ['ERR_MEDIA_MIME_GUESS_MISMATCH', [$e->getGuessedMime(), $e->getClientMime()]],

            $e instanceof MimeTypeGuessFailedException =>
                ['ERR_MEDIA_MIME_GUESS_FAILED', []],

            $e instanceof FileExtensionMismatchException =>
                ['ERR_MEDIA_EXTENSION_MISMATCH', [$e->getClientExtension(), \implode(', ', $e->getValidExtensions())]],

            $e instanceof UploadInvalidException => (function () use ($e): array {
                $key = 'EXCEPTION_FILEUPLOADERROR_' . $e->getErrorCode();
                $values = [];
                if ($e->getErrorCode() === \UPLOAD_ERR_INI_SIZE) {
                    $values[] = (string) \ini_get('upload_max_filesize');
                }
                return [$key, $values];
            })(),
        };
    }

    private function formatErrorWithFilename(\Throwable $e, string $filename): string
    {
        [$key, $values] = $this->mapExceptionToTranslation($e);
        $errorMessage = \sprintf(Registry::getLang()->translateString($key), ...$values);

        return \sprintf('%s: %s', $filename, $errorMessage);
    }

    private function sendErrorsResponse(array $errors): void
    {
        (new JsonResponse([
            'errors' => $errors,
        ]))->send();
    }
}
