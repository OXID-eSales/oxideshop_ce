<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Core\Form;

use OxidEsales\Eshop\Core\Model\FieldNameHelper;
use OxidEsales\Eshop\Core\Contract\AbstractUpdatableFields;

/**
 * Provides creators for cleaners of fields which could be updated by a customer.
 */
class UpdatableFieldsConstructor
{
    /**
     * Get cleaner for field list which are allowed to be submitted in a form.
     *
     * @param AbstractUpdatableFields $updatableFields
     *
     * @return FormFieldsCleaner
     */
    public function getAllowedFieldsCleaner(AbstractUpdatableFields $updatableFields)
    {
        $helper = oxNew(FieldNameHelper::class);
        $allowedFields = $helper->getFullFieldNames($updatableFields->getTableName(), $updatableFields->getUpdatableFields());

        $updatableFields = oxNew(\OxidEsales\Eshop\Core\Form\FormFields::class, $allowedFields);

        return oxNew(\OxidEsales\Eshop\Core\Form\FormFieldsCleaner::class, $updatableFields);
    }
}
