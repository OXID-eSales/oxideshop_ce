<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230301123522 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `oxmanufacturers` ADD column `OXICON_ALT` VARCHAR(128) NOT NULL default "" COMMENT "Alternative Icon filename" AFTER `OXICON`');
        $this->addSql('ALTER TABLE `oxmanufacturers` ADD column `OXPICTURE` VARCHAR(128) NOT NULL default "" COMMENT "Picture filename" AFTER `OXICON_ALT`');
        $this->addSql('ALTER TABLE `oxmanufacturers` ADD column `OXTHUMBNAIL` VARCHAR(128) NOT NULL default "" COMMENT "Picture thumbnail filename" AFTER `OXPICTURE`');
        $this->addSql('ALTER TABLE `oxmanufacturers` ADD column `OXPROMOTION_ICON` VARCHAR(128) NOT NULL default "" COMMENT "Icon for promotion filename" AFTER `OXTHUMBNAIL`');
    }

    public function down(Schema $schema): void
    {
    }
}
