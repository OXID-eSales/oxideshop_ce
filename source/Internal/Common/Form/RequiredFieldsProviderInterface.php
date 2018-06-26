<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Common\Form;

/**
 * Interface RequiredFieldsProviderInterface
 */
interface RequiredFieldsProviderInterface
{
    /**
     * @return array
     */
    public function getRequiredFields();
}
