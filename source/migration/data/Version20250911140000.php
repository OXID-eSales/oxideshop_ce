<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250911140000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Convert aDetailImageSizes to sDetailImageSize using oxpic1 value';
    }

    public function up(Schema $schema): void
    {
        $rows = $this->connection->fetchAllAssociative(
            "SELECT OXSHOPID, OXVARVALUE 
             FROM oxconfig 
             WHERE OXVARNAME = 'aDetailImageSizes' AND OXVARTYPE = 'aarr'"
        );

        foreach ($rows as $row) {
            $data = unserialize($row['OXVARVALUE']);

            if (is_array($data) && isset($data['oxpic1'])) {
                $query = "INSERT INTO `oxconfig` 
                          (
                              `OXID`, 
                              `OXSHOPID`, 
                              `OXVARNAME`, 
                              `OXVARTYPE`, 
                              `OXVARVALUE`
                          )
                          SELECT  
                              REPLACE(UUID(), '-', ''), 
                              ?,
                              'sDetailImageSize', 
                              'str', 
                              ?
                          WHERE NOT EXISTS (
                              SELECT `OXVARNAME` 
                              FROM `oxconfig`
                              WHERE `OXVARNAME` = 'sDetailImageSize' 
                              AND `OXSHOPID` = ?
                          )";
                $this->addSql($query, [$row['OXSHOPID'], $data['oxpic1'], $row['OXSHOPID']]);
            }
        }

        $this->addSql("DELETE FROM `oxconfig` WHERE `OXVARNAME` = 'aDetailImageSizes' AND `OXVARTYPE` = 'aarr'");
    }

    public function down(Schema $schema): void
    {
    }
}
