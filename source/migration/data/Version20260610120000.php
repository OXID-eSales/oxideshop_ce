<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260610120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove theme configuration from the oxconfig table; theme configuration is stored as YAML';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("DELETE FROM `oxconfig` WHERE `oxvarname` IN ('sTheme', 'sCustomTheme')");
        $this->addSql("DELETE FROM `oxconfig` WHERE `oxmodule` LIKE 'theme:%'");
        $this->addSql("DELETE FROM `oxconfigdisplay` WHERE `oxcfgmodule` LIKE 'theme:%'");
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException(
            'Theme configuration is no longer stored in the oxconfig table.'
        );
    }
}
