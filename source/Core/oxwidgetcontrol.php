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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

/**
 * Main shop actions controller. Processes user actions, logs
 * them (if needed), controls output, redirects according to
 * processed methods logic. This class is initialized from index.php
 */
class oxWidgetControl extends oxShopControl
{
    /**
     * Skip handler set for widget as it already set in oxShopControl.
     *
     * @var bool
     */
    protected $_blHandlerSet = true;

    /**
     * Skip main tasks as it already handled in oxShopControl.
     *
     * @var bool
     */
    protected $_blMainTasksExecuted = true;

    /**
     * Main shop widget manager. Sets needed parameters and calls parent::start method.
     *
     * Session variables:
     * <b>actshop</b>
     *
     * @param string $class      Class name
     * @param string $function   Function name
     * @param array  $parameters     Parameters array
     * @param array  $viewsChain Array of views names that should be initialized also
     */
    public function start($class = null, $function = null, $parameters = null, $viewsChain = null)
    {
        //$aParams = ( isset($aParams) ) ? $aParams : oxRegistry::getConfig()->getRequestParameter( 'oxwparams' );

        if (!isset($viewsChain) && $this->config->getRequestParameter('oxwparent')) {
            $viewsChain = explode("|", $this->config->getRequestParameter('oxwparent'));
        }

        parent::start($class, $function, $parameters, $viewsChain);

        //perform tasks that should be done at the end of widget processing
        $this->_runLast();
    }

    /**
     * Runs actions that should be performed at the controller finish.
     */
    protected function _runLast()
    {
        $oConfig = $this->config;

        if ($oConfig->hasActiveViewsChain()) {
            // Removing current active view.
            $oConfig->dropLastActiveView();

            // Setting back last active view.
            $oSmarty = oxRegistry::get("oxUtilsView")->getSmarty();
            $oSmarty->assign('oView', $oConfig->getActiveView());
        }
    }

    /**
     * Initialize and return widget view object
     *
     * @param string $sClass      view name
     * @param string $sFunction   function name
     * @param array  $aParams     Parameters array
     * @param array  $aViewsChain Array of views names that should be initialized also
     *
     * @return oxView Current active view
     */
    protected function _initializeViewObject($sClass, $sFunction, $aParams = null, $aViewsChain = null)
    {
        $oConfig = $this->config;
        $aActiveViewsNames = $oConfig->getActiveViewsNames();
        $aActiveViewsNames = array_map("strtolower", $aActiveViewsNames);

        // if exists views chain, initializing these view at first
        if (is_array($aViewsChain) && !empty($aViewsChain)) {

            foreach ($aViewsChain as $sParentClassName) {
                if ($sParentClassName != $sClass && !in_array(strtolower($sParentClassName), $aActiveViewsNames)) {
                    // creating parent view object
                    if (strtolower($sParentClassName) == 'oxubase') {
                        $oViewObject = oxNew('oxubase');
                        $oConfig->setActiveView($oViewObject);
                    } else {
                        $oViewObject = oxNew($sParentClassName);
                        $oViewObject->setClassName($sParentClassName);
                        $oConfig->setActiveView($oViewObject);
                    }
                }
            }
        }

        $oWidgetViewObject = parent::_initializeViewObject($sClass, $sFunction, $aParams);

        // Set template name for current widget.
        if (!empty($aParams['oxwtemplate'])) {
            $oWidgetViewObject->setTemplateName($aParams['oxwtemplate']);
        }

        return $oWidgetViewObject;
    }
}
