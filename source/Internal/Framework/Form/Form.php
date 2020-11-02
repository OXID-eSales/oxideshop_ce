<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Form;

class Form implements FormInterface
{
    /**
     * @var array
     */
    private $fields = [];

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var array
     */
    private $validators = [];

    public function add(FormFieldInterface $field): void
    {
        $this->fields[$field->getName()] = $field;
    }

    /**
     * @param string $name
     *
     * @return FormField
     */
    public function __get($name)
    {
        return $this->fields[$name];
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param array $request
     */
    public function handleRequest($request): void
    {
        foreach ($request as $fieldName => $value) {
            $this->$fieldName->setValue($value);
        }
    }

    public function addValidator(FormValidatorInterface $validator): void
    {
        $this->validators[] = $validator;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        $isValid = true;

        foreach ($this->validators as $validator) {
            if (true !== $validator->isValid($this)) {
                $isValid = false;

                $this->errors = array_merge(
                    $this->errors,
                    $validator->getErrors()
                );
            }
        }

        return $isValid;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
