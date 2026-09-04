<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Migration\Fixtures\Priority\Medium;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20240101000011 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
            'ALTER TABLE `test_priority_order_log` '
            . 'ADD COLUMN `via_medium` tinyint(1) NOT NULL DEFAULT 0'
        );
        $this->addSql(
            "INSERT INTO `test_priority_order_log` (`source`, `via_medium`) VALUES ('medium', 1)"
        );
    }

    public function down(Schema $schema): void
    {
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
