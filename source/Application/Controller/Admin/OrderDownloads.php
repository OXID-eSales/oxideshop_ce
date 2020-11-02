<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Admin order article manager.
 * Collects order articles information, updates it on user submit, etc.
 * Admin Menu: Orders -> Display Orders -> Articles.
 */
class OrderDownloads extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Active order object.
     *
     * @var \OxidEsales\Eshop\Application\Model\Order
     */
    protected $_oEditObject = null;

    /**
     * Executes parent method parent::render(), passes data
     * to Smarty engine, returns name of template file "order_downloads.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        if ($oOrder = $this->getEditObject()) {
            $this->_aViewData['edit'] = $oOrder;
        }

        return 'order_downloads.tpl';
    }

    /**
     * Returns editable order object.
     *
     * @return \OxidEsales\Eshop\Application\Model\Order
     */
    public function getEditObject()
    {
        $soxId = $this->getEditObjectId();
        if (null === $this->_oEditObject && isset($soxId) && '-1' !== $soxId) {
            $this->_oEditObject = oxNew(\OxidEsales\Eshop\Application\Model\OrderFileList::class);
            $this->_oEditObject->loadOrderFiles($soxId);
        }

        return $this->_oEditObject;
    }

    /**
     * Returns editable order object.
     */
    public function resetDownloadLink(): void
    {
        $sOrderFileId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('oxorderfileid');
        $oOrderFile = oxNew(\OxidEsales\Eshop\Application\Model\OrderFile::class);
        if ($oOrderFile->load($sOrderFileId)) {
            $oOrderFile->reset();
            $oOrderFile->save();
        }
    }
}
