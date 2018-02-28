<?php

namespace OxidEsales\EshopCommunity\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * change oxconfig::oxvarvalue type to mediumblob for large module lists
 */
class Version20180228160418 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $sql = "ALTER TABLE `oxconfig` CHANGE COLUMN `OXVARVALUE` `OXVARVALUE` MEDIUMBLOB NOT NULL COMMENT 'Variable value' AFTER `OXVARTYPE`;";
        $this->addSql($sql);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
