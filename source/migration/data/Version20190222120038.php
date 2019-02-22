<?php

namespace OxidEsales\EshopCommunity\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use OxidEsales\Eshop\Core\Utils;
use OxidEsales\Eshop\Core\UtilsFile;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190222120038 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $pathToXdFile = getShopBasePath() . 'xd_receiver.htm';

        if (file_exists($pathToXdFile)) {
            unlink($pathToXdFile);
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) {}
}
