<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

/**
 * Admin dynscreen manager.
 * Returns template, that arranges two other templates ("dynscreen_list.tpl"
 * and "dyn_affiliates_about.tpl") to frame.
 * @package admin
 * @subpackage dyn
 */
class Dynscreen extends oxAdminList
{
    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'dynscreen.tpl';

    /**
     * Sets up navigation for current view
     *
     * @param string $sNode None name
     *
     * @return null
     */
    protected function _setupNavigation( $sNode )
    {
        $myAdminNavig = $this->getNavigation();
        $sNode = oxConfig::getParameter( "menu" );

        // active tab
        $iActTab = oxConfig::getParameter( 'actedit' );
        $iActTab = $iActTab?$iActTab:$this->_iDefEdit;

        $sActTab = $iActTab?"&actedit=$iActTab":'';

        // list url
        $this->_aViewData['listurl'] = $myAdminNavig->getListUrl( $sNode ).$sActTab;

        // edit url
        $sEditUrl = $myAdminNavig->getEditUrl( $sNode, $iActTab ).$sActTab;
        if ( !getStr()->preg_match( "/^http(s)?:\/\//", $sEditUrl ) ) {
            //internal link, adding path
            $sEditUrl = oxRegistry::get("oxUtilsUrl")->appendParamSeparator($this->getViewConfig()->getViewConfigParam( 'selflink' )) . $sEditUrl;
        }

        $this->_aViewData['editurl'] = $sEditUrl;

        // tabs
        $this->_aViewData['editnavi'] = $myAdminNavig->getTabs( $sNode, $iActTab );

        // active tab
        $this->_aViewData['actlocation'] = $myAdminNavig->getActiveTab( $sNode, $iActTab );

        // default tab
        $this->_aViewData['default_edit'] = $myAdminNavig->getActiveTab( $sNode, $this->_iDefEdit );

        // passign active tab number
        $this->_aViewData['actedit'] = $iActTab;

        // buttons
        $this->_aViewData['bottom_buttons'] = $myAdminNavig->getBtn( $sNode );
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
