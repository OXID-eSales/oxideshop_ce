<?php

declare(strict_types=1);

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
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'widget/footer/info.tpl';

    /**
     * @var \OxidEsales\Eshop\Application\Model\ContentList
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
     * Get services content list.
     *
     * @return \OxidEsales\Eshop\Application\Model\ContentList
     */
    public function getServicesList()
    {
        $oContentList = $this->_getContentList();
        $oContentList->loadServices();

        return $oContentList;
    }

    /**
     * Returns content list object.
     *
     * @return \OxidEsales\Eshop\Application\Model\ContentList
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "getContentList" in next major
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _getContentList()
    {
        if (!$this->_oContentList) {
            $this->_oContentList = oxNew(\OxidEsales\Eshop\Application\Model\ContentList::class);
        }

        return $this->_oContentList;
    }
}
