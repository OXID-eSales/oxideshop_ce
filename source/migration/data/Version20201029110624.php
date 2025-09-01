<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201029110624 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX OXRIGHTS ON oxuser (oxrights)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX OXRIGHTS ON oxuser');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
