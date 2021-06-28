<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

/**
 * Simple variant list.
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
     * @param \OxidEsales\Eshop\Application\Model\SimpleVariant $oListObject Simple variant
     * @param array          $aDbFields   Array of available
     */
    protected function assignElement($oListObject, $aDbFields) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $oListObject->setParent($this->_oParent);
        parent::assignElement($oListObject, $aDbFields);
    }
}
