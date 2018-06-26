<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Form\ContactForm;

use OxidEsales\EshopCommunity\Internal\Common\Form\RequiredFieldsProviderInterface;

/**
 * Class ContactFormRequiredFieldsProvider
 */
class ContactFormRequiredFieldsProvider implements RequiredFieldsProviderInterface
{
    /**
     * @return array
     */
    public function getRequiredFields()
    {
        return [
            'email',
        ];
    }
}
