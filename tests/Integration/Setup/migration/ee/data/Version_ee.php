<?php
namespace OxidEsales\EshopEnterprise\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version_ee extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("CREATE TABLE migrations_test_ee ( `OXID` char(32) NOT NULL)");
    }

    public function down(Schema $schema)
    {
    }
}
