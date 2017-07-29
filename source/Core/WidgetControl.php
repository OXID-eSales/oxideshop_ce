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

namespace OxidEsales\EshopCommunity\Core;

use oxRegistry;

/**
 * Main shop actions controller. Processes user actions, logs
 * them (if needed), controls output, redirects according to
 * processed methods logic. This class is initialized from index.php
 */
class WidgetControl extends \OxidEsales\Eshop\Core\ShopControl
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
     * Array of Views added to the view chain
     *
     * @var array
     */
    protected $parentsAdded = [];

    /**
     * Main shop widget manager. Sets needed parameters and calls parent::start method.
     *
     * Session variables:
     * <b>actshop</b>
     *
     * @param string $class      Class name
     * @param string $function   Function name
     * @param array  $parameters Parameters array
     * @param array  $viewsChain Array of views names that should be initialized also
     */
    public function start($class = null, $function = null, $parameters = null, $viewsChain = null)
    {
        //$aParams = ( isset($aParams) ) ? $aParams : \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter( 'oxwparams' );

        if (!isset($viewsChain) && \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('oxwparent')) {
            $viewsChain = explode("|", \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('oxwparent'));
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
        $oConfig = $this->getConfig();

        if ($oConfig->hasActiveViewsChain()) {
            // Removing current active view.
            $oConfig->dropLastActiveView();

            foreach ($this->parentsAdded as $sParentClassName) {
                $oConfig->dropLastActiveView();
            }

            // Setting back last active view.
            $oSmarty = \OxidEsales\Eshop\Core\Registry::getUtilsView()->getSmarty();
            $oSmarty->assign('oView', $oConfig->getActiveView());
        }
    }

    /**
     * Initialize and return widget view object.
     *
     * @param string $class      View class
     * @param string $function   Function name
     * @param array  $parameters Parameters array
     * @param array  $viewsChain Array of views keys that should be initialized as well
     *
     * @return \OxidEsales\Eshop\Core\Controller\BaseController Current active view
     */
    protected function _initializeViewObject($class, $function, $parameters = null, $viewsChain = null)
    {
        $config = $this->getConfig();
        $activeViewsIds = $config->getActiveViewsIds();
        $activeViewsIds = array_map("strtolower", $activeViewsIds);
        $classKey = Registry::getControllerClassNameResolver()->getIdByClassName($class);
        $classKey = !is_null($classKey) ? $classKey : $class; //fallback

        // if exists views chain, initializing these view at first
        if (is_array($viewsChain) && !empty($viewsChain)) {
            foreach ($viewsChain as $parentClassKey) {
                $parentClass = Registry::getControllerClassNameResolver()->getClassNameById($parentClassKey);

                if ($parentClassKey != $classKey && !in_array(strtolower($parentClassKey), $activeViewsIds) && $parentClass) {
                    // creating parent view object
                    $viewObject = oxNew($parentClass);
                    if ('oxubase' != strtolower($parentClassKey)) {
                        $viewObject->setClassKey($parentClassKey);
                    }
                    $config->setActiveView($viewObject);
                    $this->parentsAdded[] = $parentClassKey;
                }
            }
        }

        $widgetViewObject = parent::_initializeViewObject($class, $function, $parameters, null);

        // Set template name for current widget.
        if (!empty($parameters['oxwtemplate'])) {
            $widgetViewObject->setTemplateName($parameters['oxwtemplate']);
        }

        return $widgetViewObject;
    }
}
