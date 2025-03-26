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
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci'
        );

        $this->addSql(
            'CREATE TABLE `oxarticle_media` (
                `id` char(32) NOT NULL,
                `articleid` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
                `mediaid` char(32) NOT NULL,
                `position` int(11) NOT NULL DEFAULT 0,
                `active` tinyint(1) NOT NULL DEFAULT 1,
                `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated` datetime NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `idx_articleid` (`articleid`),
                KEY `idx_mediaid` (`mediaid`),
                CONSTRAINT `fk_oxarticle_media_articleid` FOREIGN KEY (`articleid`)
                    REFERENCES `oxarticles` (`OXID`) ON DELETE CASCADE,
                CONSTRAINT `fk_oxarticle_media_mediaid` FOREIGN KEY (`mediaid`)
                    REFERENCES `oxmedia` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS `oxarticle_media`');
        $this->addSql('DROP TABLE IF EXISTS `oxmedia`');
    }
}
