<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20220705115226 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add 3 more pictures to manufacturers';
    }

    public function up(Schema $schema) : void
    {
        $query = "ALTER TABLE oxmanufacturers
            ADD COLUMN `OXTHUMB` varchar(128) NOT NULL default '' COMMENT 'Thumbnail filename',
            ADD COLUMN `OXPIC1` varchar(128) NOT NULL default '' COMMENT '1# Picture filename',
            ADD COLUMN `OXPIC2` varchar(128) NOT NULL default '' COMMENT '2# Picture filename',
            ADD COLUMN `OXPIC3` varchar(128) NOT NULL default '' COMMENT '3# Picture filename';";

        $this->addSql($query);
    }

    public function down(Schema $schema) : void
    {
        $query = 
            $query = "ALTER TABLE oxmanufacturers
            DROP COLUMN `OXTHUMB`,
            DROP COLUMN `OXPIC1`,
            DROP COLUMN `OXPIC2`,
            DROP COLUMN `OXPIC3`;";
            
        $this->addSql($query);
    }
}
