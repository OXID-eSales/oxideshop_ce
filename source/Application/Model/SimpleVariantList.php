<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

/**
 * Simple variant list.
 *
 */
class SimpleVariantList extends \OxidEsales\Eshop\Core\Model\ListModel
{
    /**
     * Parent article for list variants
     */
    protected $_oParent = null;

    /**
     * List Object class name
     *
     * @var string
     */
    protected $_sObjectsInListName = 'oxsimplevariant';

    /**
     * Sets parent variant
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $oParent Parent article
     */
    public function setParent($oParent)
    {
        $this->_oParent = $oParent;
    }

    /**
     * Sets parent for variant. This method is invoked for each element in oxList::assign() loop.
     *
     * @param oxSimleVariant $oListObject Simple variant
     * @param array          $aDbFields   Array of available
     */
    protected function _assignElement($oListObject, $aDbFields)
    {
        $oListObject->setParent($this->_oParent);
        parent::_assignElement($oListObject, $aDbFields);
    }
}
