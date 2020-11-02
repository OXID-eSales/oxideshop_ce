<?php

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180928072235 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $configSettingName = 'includeProductReviewLinksInEmail';
        $configSettingType = 'bool';
        $configSettingValue = '1';

        $query = "INSERT INTO `oxconfig` 
                  (
                      `OXID`, 
                      `OXSHOPID`, 
                      `OXVARNAME`, 
                      `OXVARTYPE`, 
                      `OXVARVALUE`
                  )
                  SELECT  
                      REPLACE(UUID() , '-', '' ), 
                      `OXID`,
                      ?, 
                      ?, 
                      ?
                  FROM `oxshops`                  
                  WHERE NOT EXISTS (
                      SELECT `OXVARNAME` 
                      FROM `oxconfig`
                      WHERE `OXVARNAME` = ? 
                      AND `oxconfig`.OXSHOPID = `oxshops`.OXID 
                  )";

        $this->addSql(
            $query,
            [
                $configSettingName,
                $configSettingType,
                $configSettingValue,
                $configSettingName,
            ]
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
