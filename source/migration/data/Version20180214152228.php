<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Adds config option for recommendations.
 */
class Version20180214152228 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $settingName = 'blAllowSuggestArticle';

        $this->addSql("INSERT INTO `oxconfig` (`OXID`, `OXSHOPID`, `OXMODULE`, `OXVARNAME`, `OXVARTYPE`, `OXVARVALUE`)
                            SELECT SUBSTRING(md5(uuid_short()), 1, 32),  `OXID`, '', '" . $settingName . "', 'bool', '1' FROM oxshops
                            WHERE NOT EXISTS (
                            SELECT `OXVARNAME` FROM `oxconfig` WHERE `OXVARNAME` = '" . $settingName . "'
        )");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
    }
}
