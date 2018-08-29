<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Common\Form;

/**
 * @internal
 */
interface FormFactoryInterface
{
    /**
     * @return FormInterface
     */
    public function getForm();
}
