<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230109135625 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `oxmanufacturers` ADD column `OXSORT` INT NOT NULL DEFAULT 0 AFTER `OXSHOWSUFFIX`');
        $this->addSql('CREATE INDEX OXSORT ON `oxmanufacturers` (OXSORT)');
    }

    public function down(Schema $schema): void
    {
    }
}
