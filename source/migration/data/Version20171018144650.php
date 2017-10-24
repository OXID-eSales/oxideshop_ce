<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Update from v6.0.0-rc.2 to v6.0.0-rc.3
 */
class Version20171018144650 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // All tables should have the same default character set and collation
        $this->addSql("ALTER table `oxinvitations` COLLATE utf8_general_ci;");

        $this->addSql("ALTER table `oxobject2action` COLLATE utf8_general_ci;");

        // Convert the value from the configuration variable blLoadDynContents to blSendTechnicalInformationToOxid
        $this->addSql("INSERT INTO `oxconfig` (`OXID`, `OXSHOPID`, `OXMODULE`, `OXVARNAME`, `OXVARTYPE`, `OXVARVALUE`)
          SELECT
            CONCAT(SUBSTRING(`OXID`,1, 16),SUBSTRING(REPLACE( UUID( ) ,  '-',  '' ), 17,32)) AS `OXID`,
            `OXSHOPID`,
            `OXMODULE`,
            \"blSendTechnicalInformationToOxid\" AS `OXVARNAME`,
            `OXVARTYPE`,
            `OXVARVALUE`
          FROM `oxconfig`
          WHERE `OXVARNAME` = 'blLoadDynContents'
          AND NOT EXISTS (
            SELECT `OXVARNAME` FROM `oxconfig` WHERE `OXVARNAME` = 'blSendTechnicalInformationToOxid'
          );");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
