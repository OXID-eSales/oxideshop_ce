<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Common\FormConfiguration;

/**
 * @internal
 */
interface FormFieldsConfigurationDataProviderInterface
{
    /**
     * @return array
     */
    public function getFormFieldsConfiguration();
}
