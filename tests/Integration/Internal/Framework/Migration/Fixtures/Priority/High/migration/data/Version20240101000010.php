<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Migration\Fixtures\Priority\High;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20240101000010 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE IF NOT EXISTS `test_priority_order_log` '
            . '(`id` int NOT NULL AUTO_INCREMENT, `source` varchar(50) NOT NULL, PRIMARY KEY (`id`))'
        );
        $this->addSql("INSERT INTO `test_priority_order_log` (`source`) VALUES ('high')");
    }

    public function down(Schema $schema): void
    {
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
