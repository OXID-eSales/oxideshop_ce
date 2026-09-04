<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Migration\Fixtures\Priority\Low;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20240101000012 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
            "INSERT INTO `test_priority_order_log` (`source`, `via_medium`) VALUES ('low', 1)"
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
