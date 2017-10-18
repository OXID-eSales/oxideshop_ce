<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;

/**
 * Admin dynscreen manager.
 * Returns template, that arranges two other templates ("dynscreen_list.tpl"
 * and "dyn_affiliates_about.tpl") to frame.
 *
 * @subpackage dyn
 *
 * @deprecated since v5.3 (2016-05-20); Dynpages will be removed.
 *
 */
class DynamicScreenController extends \OxidEsales\Eshop\Application\Controller\Admin\AdminListController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'dynscreen.tpl';

    /**
     * Sets up navigation for current view
     *
     * @param string $sNode None name
     */
    protected function _setupNavigation($sNode)
    {
        $myAdminNavig = $this->getNavigation();
        $sNode = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("menu");

        // active tab
        $iActTab = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('actedit');
        $iActTab = $iActTab ? $iActTab : $this->_iDefEdit;

        $sActTab = $iActTab ? "&actedit=$iActTab" : '';

        // list url
        $this->_aViewData['listurl'] = $myAdminNavig->getListUrl($sNode) . $sActTab;

        // edit url
        $sEditUrl = $myAdminNavig->getEditUrl($sNode, $iActTab) . $sActTab;
        if (!getStr()->preg_match("/^http(s)?:\/\//", $sEditUrl)) {
            //internal link, adding path
            /** @var \OxidEsales\Eshop\Core\UtilsUrl $oUtilsUrl */
            $oUtilsUrl = \OxidEsales\Eshop\Core\Registry::getUtilsUrl();
            $sSelfLinkParameter = $this->getViewConfig()->getViewConfigParam('selflink');
            $sEditUrl = $oUtilsUrl->appendParamSeparator($sSelfLinkParameter) . $sEditUrl;
        }

        $this->_aViewData['editurl'] = $sEditUrl;

        // tabs
        $this->_aViewData['editnavi'] = $myAdminNavig->getTabs($sNode, $iActTab);

        // active tab
        $this->_aViewData['actlocation'] = $myAdminNavig->getActiveTab($sNode, $iActTab);

        // default tab
        $this->_aViewData['default_edit'] = $myAdminNavig->getActiveTab($sNode, $this->_iDefEdit);

        // passign active tab number
        $this->_aViewData['actedit'] = $iActTab;

        // buttons
        $this->_aViewData['bottom_buttons'] = $myAdminNavig->getBtn($sNode);
    }

    /**
     * Returns dyn area view id
     *
     * @return string
     */
    public function getViewId()
    {
        return 'dyn_menu';
    }
}
