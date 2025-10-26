<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataMapper;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataMapper\DataMapperInterface as MediaDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRoleSet;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;

readonly class DataMapper implements DataMapperInterface
{
    public function __construct(private MediaDataMapperInterface $mediaDataMapper)
    {
    }

    public function toData(ProductMedia $productMedia): array
    {
        return [
            'id' => (string)$productMedia->getId(),
            'product_id' => (string)$productMedia->getProductId(),
            'media_id' => (string)$productMedia->getMedia()->getId(),
            'position' => $productMedia->getPosition(),
            'roles' => $this->getRolesAsValues($productMedia),
            'active' => $productMedia->isActive(),

        ];
    }

    public function fromData(array $data): ProductMedia
    {
        $roles = [];
        if (!empty($data['roles'])) {
            foreach (explode(',', $data['roles']) as $role) {
                $roles[] = ProductMediaRole::from($role);
            }
        }
        $productMedia = new ProductMedia(
            Id::fromUid($data['id']),
            Id::fromUid($data['product_id']),
            $this->mediaDataMapper->fromData(
                [
                    'id' => $data['media_id'],
                    'path' => $data['media_path'],
                    'type' => $data['media_mime_type'],
                ]
            ),
            new ProductMediaRoleSet(...$roles)
        );
        $productMedia->setPosition($data['position']);
        if (!$data['active']) {
            $productMedia->deactivate();
        }

        return $productMedia;
    }

    private function getRolesAsValues(ProductMedia $productMedia): array
    {
        return $productMedia
            ->getRoleSet()
            ->getRoles()
            ->map(static fn(ProductMediaRole $role) => $role->value())
            ->getValues();
    }
}
