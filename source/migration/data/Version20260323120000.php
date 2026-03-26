<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260323120000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE IF NOT EXISTS `oxlocales` (
                `code` varchar(10) NOT NULL,
                `name` varchar(100) NOT NULL,
                `fallback` varchar(10) NOT NULL,
                `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`code`),
                KEY `IDX_FALLBACK` (`fallback`),
                CONSTRAINT `FK_LOCALE_FALLBACK` FOREIGN KEY (`fallback`) REFERENCES `oxlocales` (`code`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci
        ");

        $this->addSql("
            CREATE TABLE IF NOT EXISTS `oxshop_locales` (
                `shop_id` int NOT NULL,
                `code` varchar(10) NOT NULL,
                `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY `UNQ_SHOP_LOCALE` (`shop_id`, `code`),
                KEY `IDX_LOCALE_CODE` (`code`),
                KEY `IDX_SHOP_ID` (`shop_id`),
                CONSTRAINT `FK_SHOP_LOCALES_LOCALE` FOREIGN KEY (`code`) REFERENCES `oxlocales` (`code`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci
        ");
    }

    public function down(Schema $schema): void
    {
    }
}
