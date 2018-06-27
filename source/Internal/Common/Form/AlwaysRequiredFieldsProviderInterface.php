<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Common\Form;

/**
 * Interface AlwaysRequiredFieldsProviderInterface
 */

interface AlwaysRequiredFieldsProviderInterface
{
    /**
     * @return array
     */
    public function getAlwaysRequiredFields();
}