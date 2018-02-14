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
 * Adds config option for recommendations.
 */
class Version20180214152228 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $facts = new Facts();
        $configFile = new ConfigFile($facts->getSourcePath().'/config.inc.php');
        $configKey = !is_null($configFile->getVar('sConfigKey')) ? $configFile->getVar('sConfigKey') : Config::DEFAULT_CONFIG_KEY;
        $settingName = 'blAllowSuggestArticle';

        $this->addSql("INSERT INTO `oxconfig` (`OXID`, `OXSHOPID`, `OXMODULE`, `OXVARNAME`, `OXVARTYPE`, `OXVARVALUE`)
                            SELECT SUBSTRING(md5(uuid_short()), 1, 32),  `OXID`, '', '".$settingName."', 'bool', ENCODE('1', '".$configKey."') FROM oxshops
                            WHERE NOT EXISTS (
                            SELECT `OXVARNAME` FROM `oxconfig` WHERE `OXVARNAME` = '".$settingName."'
        )");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
