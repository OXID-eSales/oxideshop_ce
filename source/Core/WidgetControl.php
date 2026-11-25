<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

use OxidEsales\Eshop\Application\Component\Widget\WidgetController;
use OxidEsales\Eshop\Core\Exception\RoutingException;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Exception\RequestTerminationInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Main shop actions controller. Processes user actions, logs
 * them (if needed), controls output, redirects according to
 * processed methods logic. This class is initialized from index.php
 */
class WidgetControl extends \OxidEsales\Eshop\Core\ShopControl
{
    protected bool $skipMaintenanceTasks = true;

    /**
     * Array of Views added to the view chain
     *
     * @var array
     */
    protected $parentsAdded = [];

    public function buildResponse(string $controllerKey, string $function): Response
    {
        try {
            return $this->processWidget($controllerKey, $function, [], $this->getRequestedViewsChain());
        } catch (RoutingException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            return $this->handleWidgetException($exception);
        }
    }

    public function buildIncludedWidgetResponse(string $controllerKey, array $parameters, array $viewsChain): Response
    {
        try {
            return $this->processWidget($controllerKey, '', $parameters, $viewsChain);
        } catch (Throwable $exception) {
            return $this->handleWidgetException($exception);
        }
    }

    /**
     * Runs actions that should be performed at the controller finish.
     */
    protected function runLast()
    {
        $config = Registry::getConfig();
        if ($config->getActiveViewsList() !== []) {
            $this->getRenderer()->getTemplateEngine()->addGlobal('oView', $config->getActiveView());
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
     * @throws RoutingException
     *
     * @return \OxidEsales\Eshop\Core\Controller\BaseController Current active view
     */
    protected function initializeViewObject($class, $function, $parameters = null, $viewsChain = null)
    {
        $config = \OxidEsales\Eshop\Core\Registry::getConfig();
        $activeViewsIds = $config->getActiveViewsIds();
        $activeViewsIds = array_map("strtolower", $activeViewsIds);
        $classKey = Registry::getControllerClassNameResolver()->getIdByClassName($class);
        $classKey = !is_null($classKey) ? $classKey : $class; //fallback

        if (!is_a($class, WidgetController::class, true)) {
            throw new RoutingException(sprintf('Controller "%s" is not a widget', $classKey));
        }

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

        $widgetViewObject = parent::initializeViewObject($class, $function, $parameters, null);

        // Set template name for current widget.
        if (!empty($parameters['oxwtemplate'])) {
            $widgetViewObject->setTemplateName($parameters['oxwtemplate']);
        }

        return $widgetViewObject;
    }

    private function processWidget(
        string $controllerKey,
        string $function,
        array $parameters,
        array $viewsChain,
    ): Response {
        $config = Registry::getConfig();
        $config->init();
        $controllerClass = $this->resolveControllerClass($controllerKey);

        $activeViews = $config->getActiveViewsList();
        try {
            return $this->process($controllerClass, $function, $parameters, $viewsChain);
        } finally {
            $config->setActiveViewsList($activeViews);
            $this->runLast();
        }
    }

    private function handleWidgetException(Throwable $exception): Response
    {
        if ($exception instanceof RequestTerminationInterface || $this->isDebugMode()) {
            throw $exception;
        }

        Registry::getLogger()->error($exception->getMessage(), ['exception' => $exception]);

        return new Response();
    }

    private function getRequestedViewsChain(): array
    {
        $parentViews = (string) Registry::getRequest()->getRequestEscapedParameter('oxwparent');
        if ($parentViews === '') {
            return [];
        }

        return explode('|', $parentViews);
    }

    private function getRenderer(): TemplateRendererInterface
    {
        return ContainerFacade::get(TemplateRendererBridgeInterface::class)
            ->getTemplateRenderer();
    }
}
