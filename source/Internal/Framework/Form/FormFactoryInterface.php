<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Form;

interface FormFactoryInterface
{
    /**
     * @return FormInterface
     */
    public function getForm();
}
