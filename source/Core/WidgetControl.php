<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

use OxidEsales\Eshop\Application\Component\Widget\WidgetController;
use OxidEsales\Eshop\Core\Controller\BaseController;
use OxidEsales\Eshop\Core\Exception\ObjectException;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;
use Symfony\Component\HttpFoundation\Response;

class WidgetControl extends \OxidEsales\Eshop\Core\ShopControl
{
    private array $parentsAdded = [];

    public function start(?string $class = null, ?string $function = null, ?array $parameters = null, ?array $viewsChain = null): void
    {
        if (!isset($viewsChain) && Registry::getRequest()->getRequestEscapedParameter('oxwparent')) {
            $viewsChain = explode("|", Registry::getRequest()->getRequestEscapedParameter('oxwparent'));
        }

        $response = $this->buildWidgetResponse($class, $function, $parameters, $viewsChain);
        echo $response->getContent();

        $this->runLast();
    }

    private function buildWidgetResponse(?string $class, ?string $function, ?array $parameters, ?array $viewsChain): Response
    {
        $controllerKey = $class ?? Registry::getConfig()->getRequestControllerId() ?? 'start';

        $this->initializeParentViews($viewsChain, $controllerKey);

        return $this->buildResponse($controllerKey, $function, $parameters);
    }

    protected function initializeViewObject(string $controllerKey, string $class, ?string $function, ?array $parameters = null): BaseController
    {
        $widgetViewObject = parent::initializeViewObject($controllerKey, $class, $function, $parameters);

        if (!$widgetViewObject instanceof WidgetController) {
            throw oxNew(ObjectException::class, get_class($widgetViewObject) . ' is not an instance of ' . WidgetController::class);
        }

        if (!empty($parameters['oxwtemplate'])) {
            $widgetViewObject->setTemplateName($parameters['oxwtemplate']);
        }

        return $widgetViewObject;
    }

    private function initializeParentViews(?array $viewsChain, string $classKey): void
    {
        if (empty($viewsChain)) {
            return;
        }

        $config = Registry::getConfig();
        $activeViewsIds = array_map('strtolower', $config->getActiveViewsIds());

        foreach ($viewsChain as $parentClassKey) {
            $parentClass = Registry::getControllerClassNameResolver()->getClassNameById($parentClassKey);

            if ($parentClassKey != $classKey && !in_array(strtolower($parentClassKey), $activeViewsIds) && $parentClass) {
                $viewObject = oxNew($parentClass);
                if ('oxubase' != strtolower($parentClassKey)) {
                    $viewObject->setClassKey($parentClassKey);
                }
                $config->setActiveView($viewObject);
                $this->parentsAdded[] = $parentClassKey;
            }
        }
    }

    private function runLast(): void
    {
        $config = Registry::getConfig();

        if ($config->hasActiveViewsChain()) {
            $config->dropLastActiveView();

            foreach ($this->parentsAdded as $parentClassName) {
                $config->dropLastActiveView();
            }

            $engine = $this->getRenderer()->getTemplateEngine();
            $engine->addGlobal('oView', $config->getActiveView());
        }
    }

    private function getRenderer(): TemplateRendererInterface
    {
        return ContainerFacade::get(TemplateRendererBridgeInterface::class)->getTemplateRenderer();
    }
}
