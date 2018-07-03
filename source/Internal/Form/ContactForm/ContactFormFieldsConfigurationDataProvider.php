<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Form\ContactForm;

use OxidEsales\EshopCommunity\Internal\Common\FormConfiguration\FormFieldsConfigurationDataProviderInterface;

/**
 * Class ContactFormFieldsConfigurationDataProvider
 */
class ContactFormFieldsConfigurationDataProvider implements FormFieldsConfigurationDataProviderInterface
{
    /**
     * @return array
     */
    public function getFormFieldsConfiguration()
    {
        return [
            [
                'name'  => 'email',
                'label' => 'EMAIL',
            ],
            [
                'name'  => 'firstName',
                'label' => 'FIRST_NAME',
            ],
            [
                'name'  => 'lastName',
                'label' => 'LAST_NAME',
            ],
            [
                'name'  => 'salutation',
                'label' => 'TITLE',
            ],
            [
                'name'  => 'subject',
                'label' => 'SUBJECT',
            ],
            [
                'name'  => 'message',
                'label' => 'MESSAGE',
            ],
        ];
    }
}
