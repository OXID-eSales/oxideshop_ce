<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Common\Form;

/**
 * Class FormBuilder
 */
class FormBuilder implements FormBuilderInterface
{
    /**
     * @var array
     */
    private $fields = [];

    /**
     * @inheritDoc
     */
    public function getForm()
    {
        $form = new Form();

        foreach ($this->fields as $field) {
            $form->add($field);
        }

        return $form;
    }

    /**
     * @inheritDoc
     */
    public function add($fieldName, $options = [])
    {
        $field = new FormField();
        $field->setName($fieldName);

        if (isset($options['label'])) {
            $field->setLabel($options['label']);
        }

        if (isset($options['required'])) {
            $field->setIsRequired($options['required']);
        }

        $this->fields[$fieldName] = $field;

        return $this;
    }
}
