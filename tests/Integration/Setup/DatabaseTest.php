<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Setup;

use OxidEsales\EshopCommunity\Application\Model\User;
use OxidEsales\EshopCommunity\Setup\Database;
use PHPUnit\Framework\TestCase;

final class DatabaseTest extends TestCase
{
    /** @var string  */
    private $email = 'testuser@oxideshop.dev';

    protected function tearDown(): void
    {
        $testUser = new User();
        $testUser->load($testUser->getIdByUserName($this->email));
        $testUser->delete($testUser->getId());

        parent::tearDown();
    }

    /**
     * Testing SetupDb::writeAdminLoginData()
     */
    public function testWriteAdminLoginData()
    {
        $password = 'testPassword';

        $setupDatabase = new Database();

        $setupDatabase->writeAdminLoginData($this->email, $password);

        $testUser = new User();
        $testUser->load($testUser->getIdByUserName($this->email));
        $this->assertTrue($testUser->isMallAdmin());
    }
}
