<?php

namespace OxidEsales\EshopCommunity\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\ConfigFile;
use OxidEsales\Facts\Facts;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180928072235 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
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
                      ENCODE(?, ?)
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
                $this->getConfigEncryptionKey(),
                $configSettingName,
            ]
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }

    /**
     * @return string
     */
    private function getConfigEncryptionKey(): string
    {
        $facts = new Facts();
        $configFile = new ConfigFile($facts->getSourcePath() . '/config.inc.php');

        return $configFile->getVar('sConfigKey') ?? Config::DEFAULT_CONFIG_KEY;
    }
}
