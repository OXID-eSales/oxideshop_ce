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
    public function getDescription(): string
    {
        return 'Add default locales and assign them to all shops';
    }

    public function up(Schema $schema): void
    {
        $locales = [
            ['code' => 'de_DE', 'name' => 'Deutsch (Deutschland)', 'fallback' => ''],
            ['code' => 'en_GB', 'name' => 'English (United Kingdom)', 'fallback' => 'de_DE'],
        ];

        foreach ($locales as $locale) {
            $this->addSql(
                "INSERT INTO `oxlocales` (`code`, `name`, `fallback`)
                 SELECT ?, ?, ?
                 WHERE NOT EXISTS (
                     SELECT 1 FROM `oxlocales` WHERE `code` = ?
                 )",
                [$locale['code'], $locale['name'], $locale['fallback'], $locale['code']]
            );

            $this->addSql(
                "INSERT INTO `oxshop_locales` (`shop_id`, `code`)
                 SELECT `OXID`, ?
                 FROM `oxshops`
                 WHERE NOT EXISTS (
                     SELECT 1
                     FROM `oxshop_locales`
                     WHERE `oxshop_locales`.`shop_id` = `oxshops`.`OXID`
                     AND `oxshop_locales`.`code` = ?
                 )",
                [$locale['code'], $locale['code']]
            );
        }
    }

    public function down(Schema $schema): void
    {
    }
}
