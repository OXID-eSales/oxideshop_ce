<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\ConfigFile;
use OxidEsales\Facts\Facts;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180703135728 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $facts = new Facts();
        $configFile = new ConfigFile($facts->getSourcePath() . '/config.inc.php');
        $configKey = $configFile->getVar('sConfigKey') ?? (defined('Config::DEFAULT_CONFIG_KEY') ? Config::DEFAULT_CONFIG_KEY : 'fq45QS09_fqyx09239QQ');
        $varName = 'contactFormRequiredFields';
        $varType = 'arr';
        $rawValue = serialize(['email']);

        $query = "INSERT INTO `oxconfig` 
                  (
                      `OXID`, 
                      `OXSHOPID`, 
                      `OXVARNAME`, 
                      `OXVARTYPE`, 
                      `OXVARVALUE`
                  )
                  SELECT  
                      REPLACE(UUID( ) , '-', '' ), 
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
            [$varName, $varType, $rawValue, $configKey, $varName]
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
