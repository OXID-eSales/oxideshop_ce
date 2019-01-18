<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Change oxtplblocks oxmodule field max length to 100
 * as it is in oxconfig and oxconfigdisplay tables
 */
class Version20170718124421 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE `oxtplblocks` 
          CHANGE `OXMODULE` `OXMODULE` varchar(100) 
          character set latin1 collate latin1_general_ci NOT NULL 
          COMMENT 'Module, which uses this template';");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
