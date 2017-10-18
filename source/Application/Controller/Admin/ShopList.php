<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;

/**
 * Admin shop list manager.
 * Performs collection and managing (such as filtering or deleting) function.
 * Admin Menu: Main Menu -> Core Settings.
 */
class ShopList extends \OxidEsales\Eshop\Application\Controller\Admin\AdminListController
{
    /** New Shop indicator. */
    const NEW_SHOP_ID = '-1';

    /**
     * Forces main frame update is set TRUE
     *
     * @var bool
     */
    protected $_blUpdateMain = false;

    /**
     * Default SQL sorting parameter (default null).
     *
     * @var string
     */
    protected $_sDefSortField = 'oxname';

    /**
     * Name of chosen object class (default null).
     *
     * @var string
     */
    protected $_sListClass = 'oxshop';

    /**
     * Navigation frame reload marker
     *
     * @var bool
     */
    protected $_blUpdateNav = null;

    /**
     * Executes parent method parent::render() and returns name of template
     * file "shop_list.tpl".
     *
     * @return string
     */
    public function render()
    {
        $myConfig = $this->getConfig();

        parent::render();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if (isset($soxId) && $soxId != self::NEW_SHOP_ID) {
            // load object
            $oShop = oxNew(\OxidEsales\Eshop\Application\Model\Shop::class);
            if (!$oShop->load($soxId)) {
                $soxId = $myConfig->getBaseShopId();
                $oShop->load($soxId);
            }
            $this->_aViewData['editshop'] = $oShop;
        }

        // default page number 1
        $this->_aViewData['default_edit'] = 'shop_main';
        $this->_aViewData['updatemain'] = $this->_blUpdateMain;

        $this->updateNavigation();

        if ($this->_aViewData['updatenav']) {
            //skipping requirements checking when reloading nav frame
            \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("navReload", true);
        }

        //making sure we really change shops on low level
        if ($soxId && $soxId != self::NEW_SHOP_ID) {
            $myConfig->setShopId($soxId);
            \OxidEsales\Eshop\Core\Registry::getSession()->setVariable('currentadminshop', $soxId);
        }

        return 'shop_list.tpl';
    }

    /**
     * Sets SQL WHERE condition. Returns array of conditions.
     *
     * @return array
     */
    public function buildWhere()
    {
        // we override this to add our shop if we are not malladmin
        $this->_aWhere = parent::buildWhere();
        if (!\OxidEsales\Eshop\Core\Registry::getSession()->getVariable('malladmin')) {
            // we only allow to see our shop
            $this->_aWhere[getViewName("oxshops") . ".oxid"] = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable("actshop");
        }

        return $this->_aWhere;
    }

    /**
     * Set to view data if update navigation menu.
     */
    protected function updateNavigation()
    {
    }
}
