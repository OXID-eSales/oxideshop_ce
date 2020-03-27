<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Component\Widget;

/**
 * List of additional shop information links widget.
 * Forms info link list.
 */
class Information extends \OxidEsales\Eshop\Application\Component\Widget\WidgetController
{
    /**
     * Current class template name
     *
     * @var string
     */
    protected $_sThisTemplate = 'widget/footer/info.tpl';

    /**
     * @var oxContentList
     */
    protected $_oContentList;

    /**
     * Returns service keys.
     *
     * @return array
     */
    public function getServicesKeys()
    {
        $oContentList = $this->_getContentList();

        return $oContentList->getServiceKeys();
    }

    /**
     * Get services content list
     *
     * @return array
     */
    public function getServicesList()
    {
        $oContentList = $this->_getContentList();
        $oContentList->loadServices();

        return $oContentList;
    }
    /**
     * @deprecated use self::getContentList instead
     */
    protected function _getContentList() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return $this->getContentList();
    }

    /**
     * Returns content list object.
     *
     * @return object|oxContentList
     */
    protected function getContentList()
    {
        if (!$this->_oContentList) {
            $this->_oContentList = oxNew(\OxidEsales\Eshop\Application\Model\ContentList::class);
        }

        return $this->_oContentList;
    }
}
