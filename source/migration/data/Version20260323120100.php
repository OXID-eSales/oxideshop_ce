<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260323120100 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $shopIds = $this->connection->fetchFirstColumn("SELECT OXID FROM oxshops");

        $locales = [
            ['code' => 'de_DE', 'name' => 'Deutsch (Deutschland)', 'fallback' => 'de_DE'],
            ['code' => 'en_GB', 'name' => 'English (United Kingdom)', 'fallback' => 'de_DE'],
        ];

        foreach ($locales as $locale) {
            $this->addSql(
                "INSERT IGNORE INTO `oxlocales` (`code`, `name`, `fallback`) VALUES (?, ?, ?)",
                [$locale['code'], $locale['name'], $locale['fallback']]
            );

            foreach ($shopIds as $shopId) {
                $this->addSql(
                    "INSERT IGNORE INTO `oxshop_locales` (`shop_id`, `code`) VALUES (?, ?)",
                    [(int) $shopId, $locale['code']]
                );
            }
        }
    }

    public function down(Schema $schema): void
    {
    }
}
