<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260325120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add oxmedia_attributes table for per-locale attributes on media';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE `oxmedia_attributes` (
                `id`          char(32)      NOT NULL,
                `media_id`    char(32)      NOT NULL,
                `locale_code` varchar(10)   NOT NULL,
                `shop_id`     int           NOT NULL,
                `name`        varchar(50)   NOT NULL,
                `value`       text          NOT NULL,
                `created`     datetime      NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated`     datetime      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uq_media_locale_shop_name` (`media_id`, `locale_code`, `shop_id`, `name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci'
        );
    }

    public function down(Schema $schema): void
    {
    }
}
