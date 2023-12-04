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

class ActivationDependencyValidator implements ModuleConfigurationValidatorInterface
{
    public function __construct(private readonly ModuleDependencyResolverInterface $moduleDependencyResolver)
    {
    }

    /**
     * @throws DependencyValidationException
     */
    public function validate(ModuleConfiguration $configuration, int $shopId): void
    {
        $unresolvedDependencies =
            $this->moduleDependencyResolver->getUnresolvedActivationDependencies($configuration->getId(), $shopId);

        if (!$unresolvedDependencies->hasModuleDependencies()) {
            return;
        }

        throw new DependencyValidationException(
            sprintf(
                'Module "%s" has unfulfilled dependencies in shop "%d" and can not be activated. 
                "%1$s" requires the following modules to be activated: "%s"
                Make sure all dependencies are resolved and try again.',
                $configuration->getId(),
                $shopId,
                implode(', ', $unresolvedDependencies->getModuleIds())
            )
        );
    }
}
