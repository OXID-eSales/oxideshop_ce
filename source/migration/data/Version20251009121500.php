<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251009121500 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql("DELETE FROM `oxconfig` WHERE `OXVARNAME` = 'blSendTechnicalInformationToOxid'");
    }

    public function down(Schema $schema): void
    {
    }
}
