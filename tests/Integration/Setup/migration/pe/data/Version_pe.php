<?php
namespace OxidEsales\EshopProfessional\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version_pe extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("CREATE TABLE migrations_test_pe ( `OXID` char(32) NOT NULL)");
    }

    public function down(Schema $schema)
    {
    }
}
