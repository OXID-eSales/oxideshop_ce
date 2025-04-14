<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Migrations;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use OxidEsales\EshopCommunity\Core\UtilsObject;

final class Version20250523154010 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migrates legacy product images from oxarticles to the new tables.';
    }

    public function up(Schema $schema): void
    {
        $lastProcessedId = null;

        while (true) {
            $products = $this->fetchProductsBatch($lastProcessedId);

            if (empty($products)) {
                break;
            }

            $mediaRows = [];
            $productMediaRows = [];

            foreach ($products as $product) {
                $this->collectImageDataForProduct($product, $mediaRows, $productMediaRows);
            }

            $this->batchInsertMedia($mediaRows);
            $this->batchInsertProductMedia($productMediaRows);

            $lastProcessedId = end($products)['OXID'];
        }
    }

    public function isTransactional(): bool
    {
        return false;
    }

    private function fetchProductsBatch(?string $lastProcessedId = null): array
    {
        $sql = 'SELECT OXID, OXICON, OXTHUMB, OXPIC1, OXPIC2, OXPIC3, OXPIC4, OXPIC5, OXPIC6,
                       OXPIC7, OXPIC8, OXPIC9, OXPIC10, OXPIC11, OXPIC12
                FROM oxarticles';

        $params = [];
        if ($lastProcessedId !== null) {
            $sql .= ' WHERE OXID > :lastProcessedId';
            $params['lastProcessedId'] = $lastProcessedId;
        }

        $sql .= ' LIMIT 1000';

        return $this->connection->executeQuery($sql, $params, [
            'lastProcessedId' => ParameterType::STRING,
        ])->fetchAllAssociative();
    }

    private function collectImageDataForProduct(array $product, array &$mediaRows, array &$productMediaRows): void
    {
        $productId = $product['OXID'];
        $position = 1;

        for ($i = 1; $i <= 12; $i++) {
            $picField = 'OXPIC' . $i;

            if (!empty($product[$picField])) {
                $mediaId = $this->generateUuid();
                $fileName = $product[$picField];
                $path = "master/product/$i/$fileName";

                $mediaRows[] = [$mediaId, $path];
                $productMediaRows[] = [
                    $this->generateUuid(),
                    $productId,
                    $mediaId,
                    $position,
                    'detail',
                ];

                $position++;
            }
        }

        if (!empty($product['OXICON'])) {
            $mediaId = $this->generateUuid();
            $fileName = $product['OXICON'];
            $path = "master/product/icon/$fileName";

            $mediaRows[] = [$mediaId, $path];
            $productMediaRows[] = [
                $this->generateUuid(),
                $productId,
                $mediaId,
                0,
                'icon',
            ];
        }

        if (!empty($product['OXTHUMB'])) {
            $mediaId = $this->generateUuid();
            $fileName = $product['OXTHUMB'];
            $path = "master/product/thumb/$fileName";

            $mediaRows[] = [$mediaId, $path];
            $productMediaRows[] = [
                $this->generateUuid(),
                $productId,
                $mediaId,
                0,
                'thumbnail',
            ];
        }
    }

    private function batchInsertMedia(array $mediaRows): void
    {
        if (empty($mediaRows)) {
            return;
        }

        $chunks = array_chunk($mediaRows, 1000);

        foreach ($chunks as $chunk) {
            $values = [];
            $params = [];

            foreach ($chunk as $row) {
                $values[] = '(?, ?, \'IMAGE\', NOW())';
                array_push($params, ...$row);
            }

            $sql = 'INSERT INTO oxmedia (
                     id,
                     path,
                     type,
                     created
                     ) VALUES ' . implode(', ', $values);
            $this->connection->executeStatement($sql, $params);
        }
    }

    private function batchInsertProductMedia(array $productMediaRows): void
    {
        if (empty($productMediaRows)) {
            return;
        }

        $chunks = array_chunk($productMediaRows, 1000);

        foreach ($chunks as $chunk) {
            $values = [];
            $params = [];

            foreach ($chunk as $row) {
                $values[] = '(?, ?, ?, ?, ?, 1, NOW())';
                array_push($params, ...$row);
            }

            $sql = 'INSERT INTO oxproduct_media (
                             id,
                             product_id,
                             media_id,
                             position,
                             type,
                             active,
                             created
                             ) VALUES '
            . implode(', ', $values);

            $this->connection->executeStatement($sql, $params);
        }
    }

    private function generateUuid(): string
    {
        return UtilsObject::getInstance()->generateUId();
    }
}
