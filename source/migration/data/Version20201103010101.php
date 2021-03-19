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

final class Version20201103010101 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $table = $schema->getTable('oxdeliveryset');
        $table->addColumn('OXTRACKINGURL', Types::STRING);
    }

    public function down(Schema $schema): void
    {
        $table = $schema->getTable('oxdeliveryset');
        $table->dropColumn('OXTRACKINGURL');
    }
}
