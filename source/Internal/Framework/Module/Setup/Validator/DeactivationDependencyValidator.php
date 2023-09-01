<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Validator;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\DependencyValidationException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleDependencyResolverInterface;

class DeactivationDependencyValidator implements ModuleConfigurationValidatorInterface
{
    public function __construct(private readonly ModuleDependencyResolverInterface $moduleDependencyResolver)
    {
    }

    /**
     * @throws DependencyValidationException
     */
    public function validate(ModuleConfiguration $configuration, int $shopId): void
    {
        if ($this->moduleDependencyResolver->canDeactivateModule($configuration->getId(), $shopId)) {
            return;
        }

        throw new DependencyValidationException(
            sprintf(
                'Module "%s" in shop "%d" has unfulfilled dependencies and can not be deactivated.
                Make sure all its dependencies are deactivated and try again.',
                $configuration->getId(),
                $shopId
            )
        );
    }
}
