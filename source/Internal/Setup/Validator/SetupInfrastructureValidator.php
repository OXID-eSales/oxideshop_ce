<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Validator;

use OxidEsales\EshopCommunity\Internal\Setup\Database\SetupDbConnectionValidatorInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Directory\DirectoryValidatorInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Htaccess\HtaccessDaoFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Parameters\SetupParameters;

class SetupInfrastructureValidator implements SetupInfrastructureValidatorInterface
{
    private SetupParameters $parameters;

    public function __construct(
        private readonly DirectoryValidatorInterface $directoriesValidator,
        private readonly HtaccessDaoFactoryInterface $htaccessDaoFactory,
        private readonly SetupDbConnectionValidatorInterface $setupDbConnectionValidator,
    ) {
    }

    public function validate(SetupParameters $setupParameters): void
    {
        $this->parameters = $setupParameters;
        $this->checkShopDirectoryPermissions();
        $this->checkWebServerConfigFilePermissions();
        $this->checkDatabaseServerConnectivity();
    }

    private function checkShopDirectoryPermissions(): void
    {
        $this->directoriesValidator
            ->checkPathIsAbsolute(
                $this->parameters->getCacheDir()
            );
        $this->directoriesValidator
            ->validateDirectory(
                $this->parameters->getCacheDir(),
            );
    }

    private function checkWebServerConfigFilePermissions(): void
    {
        $this->htaccessDaoFactory->createRootHtaccessDao();
    }

    private function checkDatabaseServerConnectivity(): void
    {
        $this->setupDbConnectionValidator
            ->validate(
                $this->parameters->getDbConfig()
            );
    }
}
