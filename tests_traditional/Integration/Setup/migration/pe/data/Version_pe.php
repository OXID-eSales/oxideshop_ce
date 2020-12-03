<?php

namespace OxidEsales\EshopProfessional\Migrations;


use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version_pe extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE migrations_test_pe ( `OXID` char(32) NOT NULL)");
    }

    public function down(Schema $schema): void
    {
    }
}
