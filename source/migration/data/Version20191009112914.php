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
class Version20191009112914 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql(
            'ALTER TABLE oxconfig ADD COLUMN OXVARVALUE_TEXT TEXT NULL'
        );
        $this->addSql(
            'UPDATE oxconfig SET OXVARVALUE_TEXT = CONVERT(oxvarvalue, CHAR)'
        );
        $this->addSql(
            'ALTER TABLE oxconfig DROP COLUMN OXVARVALUE'
        );
        $this->addSql(
            'ALTER TABLE oxconfig CHANGE COLUMN OXVARVALUE_TEXT OXVARVALUE text NULL COMMENT \'Variable value\' AFTER OXVARTYPE'
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
