<?php
namespace OxidEsales\EshopCommunity\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version_ce extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("CREATE TABLE migrations_test_ce ( `OXID` char(32) NOT NULL)");
    }

    public function down(Schema $schema)
    {
    }
}
