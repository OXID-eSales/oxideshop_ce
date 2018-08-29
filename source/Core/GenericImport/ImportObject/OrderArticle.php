<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\GenericImport\ImportObject;

/**
 * Import object for Order Articles.
 */
class OrderArticle extends \OxidEsales\Eshop\Core\GenericImport\ImportObject\ImportObject
{
    /** @var string Database table name. */
    protected $tableName = 'oxorderarticles';

    /** @var string Shop object name. */
    protected $shopObjectName = 'oxorderarticle';

    /**
     * issued before saving an object. can modify aData for saving
     *
     * @param \OxidEsales\Eshop\Core\Model\BaseModel $shopObject        oxBase child for object
     * @param array                                  $data              Data for object
     * @param bool                                   $allowCustomShopId If true then AllowCustomShopId
     *
     * @return array
     */
    protected function preAssignObject($shopObject, $data, $allowCustomShopId)
    {
        $data = parent::preAssignObject($shopObject, $data, $allowCustomShopId);

        // check if data is not serialized
        $persParamValues = @unserialize($data['OXPERSPARAM']);
        if (!is_array($persParamValues)) {
            // data is a string with | separation, prepare for oxid
            $persParamValues = explode("|", $data['OXPERSPARAM']);
            $data['OXPERSPARAM'] = serialize($persParamValues);
        }
        if (array_key_exists('OXORDERSHOPID', $data)) {
            $data['OXORDERSHOPID'] = $this->getOrderShopId($data['OXORDERSHOPID']);
        }

        return $data;
    }

    /**
     * Returns formed order shop id, which should be set to data array.
     *
     * @param string $currentShopId
     *
     * @return string
     */
    protected function getOrderShopId($currentShopId)
    {
        return 1;
    }
}
