<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211117193324 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Update oxcontents text fields sizes';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql("ALTER TABLE `oxcontents` MODIFY column OXCONTENT MEDIUMTEXT NOT NULL COMMENT 'Content (multilanguage)'");
        $this->addSql("ALTER TABLE `oxcontents` MODIFY column OXCONTENT_1 MEDIUMTEXT NOT NULL");
        $this->addSql("ALTER TABLE `oxcontents` MODIFY column OXCONTENT_2 MEDIUMTEXT NOT NULL");
        $this->addSql("ALTER TABLE `oxcontents` MODIFY column OXCONTENT_3 MEDIUMTEXT NOT NULL");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}