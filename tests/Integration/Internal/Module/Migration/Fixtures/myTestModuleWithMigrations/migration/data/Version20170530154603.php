<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Test migration to create data which could be used to check if Migrations actually works.
 */
class Version20170530154603 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE `test_doctrine_migration_wrapper` (`id` char(255) NOT NULL);');
        $this->addSql("INSERT INTO `test_doctrine_migration_wrapper` (`id`) VALUES ('shop_migration');");
    }

    public function down(Schema $schema): void
    {
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
