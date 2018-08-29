<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\GenericImport\ImportObject;

/**
 * Import object for Articles.
 */
class Article extends \OxidEsales\Eshop\Core\GenericImport\ImportObject\ImportObject
{
    /** @var string Database table name. */
    protected $tableName = 'oxarticles';

    /** @var string Shop object name. */
    protected $shopObjectName = 'oxArticle';

    /**
     * Imports article. Returns import status.
     *
     * @param array $data DB row array.
     *
     * @return string $oxid Id on success, bool FALSE on failure.
     */
    public function import($data)
    {
        if (isset($data['OXID'])) {
            $this->checkIdField($data['OXID']);
        }

        return parent::import($data);
    }

    /**
     * Issued before saving an object.
     * Can modify $data array before saving.
     * Set default value of OXSTOCKFLAG to 1 according to eShop admin functionality.
     *
     * @param \OxidEsales\Eshop\Core\Model\BaseModel $shopObject        shop object
     * @param array                                  $data              data to prepare
     * @param bool                                   $allowCustomShopId if allow custom shop id
     *
     * @return array
     */
    protected function preAssignObject($shopObject, $data, $allowCustomShopId)
    {
        if (!isset($data['OXSTOCKFLAG'])) {
            if (!$data['OXID'] || !$shopObject->exists($data['OXID'])) {
                $data['OXSTOCKFLAG'] = 1;
            }
        }

        return parent::preAssignObject($shopObject, $data, $allowCustomShopId);
    }

    /**
     * Post saving hook. can finish transactions if needed or ajust related data.
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $shopObject Shop object.
     * @param array                                       $data       Data to save.
     *
     * @return mixed data to return
     */
    protected function postSaveObject($shopObject, $data)
    {
        $articleId = $shopObject->getId();
        $shopObject->onChange(null, $articleId, $articleId);

        return $articleId;
    }

    /**
     * Creates shop object.
     *
     * @return \OxidEsales\Eshop\Core\Model\BaseModel
     */
    protected function createShopObject()
    {
        /** @var \OxidEsales\Eshop\Application\Model\Article $shopObject */
        $shopObject = parent::createShopObject();
        $shopObject->setNoVariantLoading(true);

        return $shopObject;
    }
}
