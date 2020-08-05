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
    protected $_sThisTemplate = 'widget/footer/info';

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
        $oContentList = $this->getContentList();

        return $oContentList->getServiceKeys();
    }

    /**
     * Get services content list
     *
     * @return \OxidEsales\Eshop\Application\Model\ContentList
     */
    public function getServicesList()
    {
        $oContentList = $this->getContentList();
        $oContentList->loadServices();

        return $oContentList;
    }

    /**
     * Returns content list object.
     *
     * @return \OxidEsales\Eshop\Application\Model\ContentList
     */
    protected function getContentList()
    {
        if (!$this->_oContentList) {
            $this->_oContentList = oxNew(\OxidEsales\Eshop\Application\Model\ContentList::class);
        }

        return $this->_oContentList;
    }
}
