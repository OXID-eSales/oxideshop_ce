<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231128113123 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add Low Stock Message';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'ALTER TABLE `oxarticles` ADD column `OXLOWSTOCKTEXT` TEXT NOT NULL default "" COMMENT '
                . '"Message, which is shown if the article is in low stock (multilanguage)"'
        );
        $this->addSql(
            'ALTER TABLE `oxarticles` ADD column `OXLOWSTOCKTEXT_1` TEXT NOT NULL default'
        );
        $this->addSql(
            'ALTER TABLE `oxarticles` ADD column `OXLOWSTOCKTEXT_2` TEXT NOT NULL default'
        );
        $this->addSql(
            'ALTER TABLE `oxarticles` ADD column `OXLOWSTOCKTEXT_3` TEXT NOT NULL default'
        );
        $this->addSql(
            'ALTER TABLE `oxarticles` ADD column `OXLOWSTOCKACTIVE` TINYINT(1)'
        );
    }

    public function down(Schema $schema): void
    {
    }
}
