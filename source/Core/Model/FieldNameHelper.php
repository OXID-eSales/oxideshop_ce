<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Model;

/**
 * Helper to work with field names of a model.
 *
 * @internal Do not make a module extension for this class.
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class FieldNameHelper
{
    /**
     * Return field names with and without table name as a prefix.
     *
     * @param string $tableName
     * @param array  $fieldNames
     *
     * @return array
     */
    public function getFullFieldNames($tableName, $fieldNames)
    {
        $combinedFields = [];
        $tablePrefix = strtolower($tableName) . '__';
        foreach ($fieldNames as $fieldName) {
            $fieldName = strtolower($fieldName);

            $fieldNameWithoutTableName = str_replace($tablePrefix, '', $fieldName);
            $combinedFields[] = $fieldNameWithoutTableName;

            if (strpos($fieldName, $tablePrefix) !== 0) {
                $fieldName = $tablePrefix . $fieldName;
            }

            $combinedFields[] = $fieldName;
        }

        return $combinedFields;
    }
}
