<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250320120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add Media Tables with lowercase columns';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE `oxmedia` (
                `id` char(32) NOT NULL,
                `path` varchar(255) NOT NULL,
                `type` varchar(32) NOT NULL,
                `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            )'
        );

        $this->addSql(
            'CREATE TABLE `oxproduct_media` (
                `id` char(32) NOT NULL,
                `product_id` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
                `media_id` char(32) NOT NULL,
                `position` int(11) NOT NULL DEFAULT 0,
                `active` tinyint(1) NOT NULL DEFAULT 1,
                `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                INDEX `product_id` (`product_id`),
                INDEX `media_id` (`media_id`)
            )'
        );

        $this->addSql(
            'CREATE TABLE `oxproduct_media_roles` (
                `product_media_id` char(32) NOT NULL,
                `role` varchar(32) NOT NULL,
                `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`product_media_id`, `role`)
                )'
        );
    }

    public function down(Schema $schema): void
    {
    }
}
