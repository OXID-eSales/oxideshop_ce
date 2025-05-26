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
        return 'Migrates legacy product images from oxarticles to existing oxmedia and oxarticle_media tables.';
    }

    public function up(Schema $schema): void
    {
        $lastProcessedId = null;

        while (true) {
            $articles = $this->fetchArticlesBatch($lastProcessedId);

            if (empty($articles)) {
                break;
            }

            $mediaRows = [];
            $articleMediaRows = [];

            foreach ($articles as $article) {
                $this->collectImageDataForArticle($article, $mediaRows, $articleMediaRows);
            }

            $this->batchInsertMedia($mediaRows);
            $this->batchInsertArticleMedia($articleMediaRows);

            $lastProcessedId = end($articles)['OXID'];
        }
    }

    public function isTransactional(): bool
    {
        return false;
    }

    private function fetchArticlesBatch(?string $lastProcessedId = null): array
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

    private function collectImageDataForArticle(array $article, array &$mediaRows, array &$articleMediaRows): void
    {
        $articleId = $article['OXID'];
        $position = 1;

        for ($i = 1; $i <= 12; $i++) {
            $picField = 'OXPIC' . $i;

            if (!empty($article[$picField])) {
                $mediaId = $this->generateUuid();
                $fileName = $article[$picField];
                $path = "master/product/$i/$fileName";

                $mediaRows[] = [$mediaId, $path];
                $articleMediaRows[] = [
                    $this->generateUuid(),
                    $articleId,
                    $mediaId,
                    $position,
                    'detail',
                ];

                $position++;
            }
        }

        if (!empty($article['OXICON'])) {
            $mediaId = $this->generateUuid();
            $fileName = $article['OXICON'];
            $path = "master/product/icon/$fileName";

            $mediaRows[] = [$mediaId, $path];
            $articleMediaRows[] = [
                $this->generateUuid(),
                $articleId,
                $mediaId,
                0,
                'icon',
            ];
        }

        if (!empty($article['OXTHUMB'])) {
            $mediaId = $this->generateUuid();
            $fileName = $article['OXTHUMB'];
            $path = "master/product/thumb/$fileName";

            $mediaRows[] = [$mediaId, $path];
            $articleMediaRows[] = [
                $this->generateUuid(),
                $articleId,
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

            $sql = 'INSERT INTO oxmedia (id, path, type, created) VALUES ' . implode(', ', $values);
            $this->connection->executeStatement($sql, $params);
        }
    }

    private function batchInsertArticleMedia(array $articleMediaRows): void
    {
        if (empty($articleMediaRows)) {
            return;
        }

        $chunks = array_chunk($articleMediaRows, 1000);

        foreach ($chunks as $chunk) {
            $values = [];
            $params = [];

            foreach ($chunk as $row) {
                $values[] = '(?, ?, ?, ?, ?, 1, NOW())';
                array_push($params, ...$row);
            }

            $sql = 'INSERT INTO oxarticle_media (id, articleid, mediaid, position, type, active, created) VALUES '
            . implode(', ', $values);

            $this->connection->executeStatement($sql, $params);
        }
    }

    private function generateUuid(): string
    {
        return UtilsObject::getInstance()->generateUId();
    }
}
