<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\GenericImport\ImportObject;

/**
 * Import object for Categories.
 */
class Category extends \OxidEsales\Eshop\Core\GenericImport\ImportObject\ImportObject
{
    /**
     * @var string database table name
     */
    protected $tableName = 'oxcategories';

    /**
     * @var string shop object name
     */
    protected $shopObjectName = 'oxcategory';

    /**
     * Issued before saving an object. can modify aData for saving.
     *
     * @param \OxidEsales\Eshop\Core\Model\BaseModel $shopObject        shop object
     * @param array                                  $data              data to prepare
     * @param bool                                   $allowCustomShopId if allow custom shop id
     *
     * @return array
     */
    protected function preAssignObject($shopObject, $data, $allowCustomShopId)
    {
        $data = parent::preAssignObject($shopObject, $data, $allowCustomShopId);

        if (!$data['OXPARENTID']) {
            $data['OXPARENTID'] = 'oxrootid';
        }

        return $data;
    }
}
