<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

use OxidEsales\Eshop\Core\Exception\FileException;
use OxidEsales\Eshop\Core\Exception\RoutingException;
use OxidEsales\Eshop\Core\Exception\SystemComponentException;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Controller\ViewControllerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class ShopControl extends \OxidEsales\Eshop\Core\Base
{
    private ?array $errors = null;
    private ?array $allErrors = null;
    private ?array $controllerErrors = null;

    public function buildResponse(string $controllerKey, ?string $function = null, ?array $parameters = null): Response
    {
        $this->executeMaintenanceTasks();

        $class = $this->resolveControllerClass($controllerKey);
        $view = $this->initializeViewObject($controllerKey, $class, $function, $parameters);

        $this->executeAction($view, $function);

        $output = $this->formOutput($view);
        $charset = $view->getCharSet();

        if (Registry::getRequest()->getRequestEscapedParameter('renderPartial')) {
            $output = json_encode([
                'content' => $output,
                'errors' => $this->getFormattedErrors($view->getClassKey()),
            ]);
            $response = new Response($output, 200, ['Content-Type' => "application/json; charset={$charset}"]);
        } else {
            $response = new Response($output, 200, ['Content-Type' => "text/html; charset={$charset}"]);
        }

        return $response;
    }

    public function buildAjaxResponse(string $container, ?string $function = null): Response
    {
        $config = Registry::getConfig();
        $config->init();

        $utilModule = $config->getConfigParam('sUtilModule');
        if ($utilModule && file_exists(getShopBasePath() . 'modules/' . $utilModule)) {
            include_once getShopBasePath() . 'modules/' . $utilModule;
        }

        $config->setConfigParam('blAdmin', true);

        if (!$this->isAdminAjaxAuthorized()) {
            return new RedirectResponse('index.php');
        }

        $container = strtolower(trim(basename($container)));
        $ajaxComponent = $this->resolveAjaxComponent($container);

        ob_start();
        try {
            $ajaxComponent->setName($container);
            $ajaxComponent->processRequest($function);
        } finally {
            $body = ob_get_clean() ?: '';
        }

        $config->pageClose();

        return new Response($body);
    }

    private function isAdminAjaxAuthorized(): bool
    {
        return Registry::getSession()->checkSessionChallenge()
            && count(Registry::getUtilsServer()->getOxCookie())
            && Registry::getUtils()->checkAccessRights();
    }

    private function resolveAjaxComponent(string $container): object
    {
        $ajaxContainerClassName = $container . '_ajax';
        try {
            $containerClass = Registry::getControllerClassNameResolver()->getClassNameById($ajaxContainerClassName);
            return oxNew($containerClass);
        } catch (SystemComponentException) {
            $exception = new FileException();
            $exception->setMessage('EXCEPTION_FILENOTFOUND ' . $ajaxContainerClassName);
            throw $exception;
        }
    }

    protected function resolveControllerClass(string $controllerKey): string
    {
        return Registry::getControllerClassNameResolver()->getClassNameById($controllerKey)
            ?? throw new RoutingException("Controller \"{$controllerKey}\" cannot be resolved");
    }

    protected function initializeViewObject(string $controllerKey, string $class, ?string $function, ?array $parameters = null): ViewControllerInterface
    {
        $controller = $this->isServiceController($controllerKey, $class)
            ? ContainerFacade::get($class)
            : oxNew($class);

        $controller->setClassKey($controllerKey);
        $controller->setFncName($function);
        $controller->setViewParameters($parameters);
        Registry::getConfig()->setActiveView($controller);
        $controller->init();

        return $controller;
    }

    protected function executeAction(ViewControllerInterface $view, ?string $function): void
    {
        if ($function && method_exists($view, $function) && !(new \ReflectionMethod($view, $function))->isPublic()) {
            throw new RoutingException("Non public method cannot be accessed: " . get_class($view) . "::{$function}");
        }

        $view->executeFunction($function);
    }

    protected function formOutput(ViewControllerInterface $view): string
    {
        return $this->render($view);
    }

    protected function render(ViewControllerInterface $view): string
    {
        $templateName = $view->render();
        $renderer = $this->getRenderer();
        $viewData = $this->attachSessionErrors($view, $view->getViewData());

        try {
            return $renderer->renderTemplate($templateName, $viewData);
        } catch (\Throwable $e) {
            Registry::getLogger()->error("Template \"{$templateName}\" render failed: {$e->getMessage()}", [$e]);
            return $renderer->renderTemplate('message/exception', $viewData);
        }
    }

    private function getFormattedErrors(string $controllerName): array
    {
        $formatted = [];
        foreach ($this->loadErrors($controllerName) as $location => $items) {
            foreach ($items as $key => $serialized) {
                $formatted[$location][$key] = unserialize($serialized)->getOxMessage();
            }
        }
        return $formatted;
    }

    protected function loadErrors(string $controllerName): array
    {
        if (null === $this->errors) {
            $this->errors = Registry::getSession()->getVariable('Errors') ?? [];
            $this->controllerErrors = Registry::getSession()->getVariable('ErrorController');
            $this->allErrors = $this->errors;
        }

        if (!empty($this->controllerErrors) && is_array($this->controllerErrors)) {
            foreach ($this->controllerErrors as $name => $controller) {
                if ($controller === $controllerName) {
                    unset($this->allErrors[$name], $this->controllerErrors[$name]);
                }
            }
        } else {
            $this->allErrors = [];
        }

        Registry::getSession()->setVariable('ErrorController', $this->controllerErrors);
        Registry::getSession()->setVariable('Errors', $this->allErrors);

        return $this->errors;
    }

    private function attachSessionErrors(ViewControllerInterface $view, array $viewData): array
    {
        $errors = $this->loadErrors($view->getClassKey());
        if (!empty($errors)) {
            Registry::getUtilsView()->passAllErrorsToView($viewData, $errors);
        }
        return $viewData;
    }

    private function getRenderer(): TemplateRendererInterface
    {
        return ContainerFacade::get(TemplateRendererBridgeInterface::class)->getTemplateRenderer();
    }

    private function isServiceController(string $controllerKey, string $class): bool
    {
        return isset(ContainerFacade::getParameter('oxid.view_controllers_map')[$controllerKey])
            && ContainerFacade::has($class);
    }

    protected function executeMaintenanceTasks(): void
    {
        oxNew(\OxidEsales\Eshop\Application\Model\ArticleList::class)->updateUpcomingPrices();
    }
}
