<?php

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191007144155 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $query = "ALTER TABLE oxseo 
            DROP INDEX `OXOBJECTID`, 
            ADD INDEX `OXOBJECTID` (`OXOBJECTID`,`OXSHOPID`,`OXLANG`)";

        $this->addSql($query);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
