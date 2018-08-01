<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Configuration\Module\Service;

use OxidEsales\EshopCommunity\Internal\Configuration\Module\Dao\JsonFileDao;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\Service\RepositoryService;
use PHPUnit\Framework\TestCase;
use SplFileObject;
use SplTempFileObject;

class RepositoryServiceTest extends TestCase
{
    public function testRepositoryServiceRetrievesConfiguration()
    {
        $file = $this->getFile();
        $dao = new JsonFileDao($file);
        $repositoryService = new RepositoryService($dao);
        $configuration = $repositoryService->getConfiguration();

        $environmentNames = $configuration->getEnvironmentNames();

        $this->assertEquals(
            [
                'dev',
                'testing',
                'staging',
                'production'
            ],
            $environmentNames
        );
    }

    /**
     * @return SplFileObject
     */
    private function getFile()
    {
        return new SplTempFileObject();
    }
}
