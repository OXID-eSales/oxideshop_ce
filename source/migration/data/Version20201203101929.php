<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * change oxuserbasket::oxpublic default value to 0
 */
final class Version20201203101929 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $query = "ALTER TABLE `oxuserbaskets` 
                ALTER `OXPUBLIC` SET DEFAULT '0';";

        $this->addSql($query);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
