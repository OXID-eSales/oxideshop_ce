<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

use OxidEsales\Eshop\Application\Component\Widget\WidgetController;
use OxidEsales\Eshop\Core\Exception\ObjectException;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;

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
     * @deprecated since v.6.0.0 (2017-10-11); Not used any more, was used in _setDefaultExceptionHandler()
     * which was already removed.
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
            $engine = $this->getRenderer()->getTemplateEngine();
            $engine->addGlobal('oView', $oConfig->getActiveView());
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
     * @throws ObjectException
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

        if (!is_a($widgetViewObject, WidgetController::class)) {
            /** @var ObjectException $exception */
            $exception = oxNew(ObjectException::class, get_class($widgetViewObject) . ' is not an instance of ' . WidgetController::class);
            throw $exception;
        }

        // Set template name for current widget.
        if (!empty($parameters['oxwtemplate'])) {
            $widgetViewObject->setTemplateName($parameters['oxwtemplate']);
        }

        return $widgetViewObject;
    }

    /**
     * @internal
     *
     * @return TemplateRendererInterface
     */
    private function getRenderer()
    {
        return $this->getContainer()
            ->get(TemplateRendererBridgeInterface::class)
            ->getTemplateRenderer();
    }
}
