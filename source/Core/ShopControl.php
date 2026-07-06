<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

use OxidEsales\Eshop\Application\Model\ArticleList;
use OxidEsales\Eshop\Core\Contract\IDisplayError;
use OxidEsales\Eshop\Core\Exception\RoutingException;
use OxidEsales\Eshop\Core\Exception\StandardException;
use OxidEsales\Eshop\Core\Exception\SystemComponentException;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Controller\ViewControllerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Http\ResponseReady;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;
use OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\ViewRenderedEvent;
use ReflectionMethod;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ShopControl extends \OxidEsales\Eshop\Core\Base
{
    protected bool $skipMaintenanceTasks = false;

    protected ?array $errors = null;

    protected ?array $remainingErrors = null;

    protected ?array $errorControllers = null;

    public function buildResponse($controllerKey = null, $function = null, $parameters = null, $viewsChain = null): Response
    {
        Registry::getConfig()->init();

        $function ??= Registry::getRequest()->getRequestEscapedParameter('fnc');
        $controllerKey ??= $this->getStartControllerKey();
        $controllerClass = $this->resolveControllerClass($controllerKey);

        return $this->process($controllerClass, $function, $parameters, $viewsChain);
    }

    protected function getStartControllerKey(): string
    {
        $controllerKey = Registry::getConfig()->getRequestControllerId();

        if (!$controllerKey) {
            $session = Registry::getSession();
            if ($this->isAdmin()) {
                $controllerKey = $session->getVariable('auth') ? 'admin_start' : 'login';
            } else {
                $controllerKey = $this->getFrontendStartControllerKey();
            }
            $session->setVariable('cl', $controllerKey);
        }

        return $controllerKey;
    }

    /**
     * @throws RoutingException
     */
    protected function resolveControllerClass(string $controllerKey): string
    {
        $resolvedClass = Registry::getControllerClassNameResolver()->getClassNameById($controllerKey);

        if (!$resolvedClass) {
            throw new RoutingException(sprintf('Controller "%s" cannot be resolved', $controllerKey));
        }

        return $resolvedClass;
    }

    protected function getFrontendStartControllerKey()
    {
        return 'start';
    }

    /**
     * @param string $class      Class name
     * @param string $function   Name of function
     * @param array  $parameters Parameters array
     * @param array  $viewsChain Array of views names that should be initialized also
     */
    protected function process($class, $function, $parameters = null, $viewsChain = null): Response
    {
        $this->executeMaintenanceTasks();

        $view = $this->initializeViewObject($class, $function, $parameters, $viewsChain);

        $this->executeAction($view, $view->getFncName());

        $output = $this->formOutput($view);

        ContainerFacade::dispatch(new ViewRenderedEvent($this));

        $response = $this->createResponse($view, $output);

        Registry::getConfig()->pageClose();

        return $response;
    }

    protected function executeMaintenanceTasks(): void
    {
        if ($this->skipMaintenanceTasks) {
            return;
        }

        oxNew(ArticleList::class)->updateUpcomingPrices();
    }

    /**
     * Executes provided function on view object.
     * If this function can not be executed (is protected or so), a RoutingException is thrown
     *
     * @param FrontendController $view
     * @param string             $functionName
     */
    protected function executeAction($view, $functionName)
    {
        if (!$this->canExecuteFunction($view, $functionName)) {
            throw new RoutingException(
                sprintf("Non public method cannot be accessed: %s::%s", get_class($view), $functionName)
            );
        }

        $view->executeFunction($functionName);
    }

    /**
     * Forms output from view object.
     *
     * @param FrontendController $view
     *
     * @return string
     */
    protected function formOutput($view)
    {
        return $this->render($view);
    }

    /**
     * Initialize and return view object.
     *
     * @param string $class      View class
     * @param string $function   Function name
     * @param array  $parameters Parameters array
     * @param array  $viewsChain Array of views names that should be initialized also
     *
     * @return FrontendController
     */
    protected function initializeViewObject($class, $function, $parameters = null, $viewsChain = null)
    {
        $classKey = Registry::getControllerClassNameResolver()->getIdByClassName($class);
        $classKey = !is_null($classKey) ? $classKey : $class; //fallback

        /** @var ViewControllerInterface $controller */
        $controller = $this->isServiceController($classKey, $class)
            ? ContainerFacade::get($class)
            : oxNew($class);

        $controller->setClassKey($classKey);
        $controller->setFncName($function);
        $controller->setViewParameters($parameters);

        Registry::getConfig()->setActiveView($controller);

        $this->onViewCreation($controller);

        $controller->init();

        return $controller;
    }

    /**
     * Event for any actions during view creation.
     *
     * @param FrontendController $view
     */
    protected function onViewCreation($view)
    {
    }

    /**
     * Check if method can be executed.
     *
     * @param FrontendController $view     View object to check if its method can be executed.
     * @param string             $function Method to check if it can be executed.
     *
     * @return bool
     */
    protected function canExecuteFunction($view, $function)
    {
        if (!$function || !method_exists($view, $function)) {
            return true;
        }

        return new ReflectionMethod($view, $function)->isPublic();
    }

    /**
     * Format error messages from _getErrors and return as array.
     *
     * @param string $controllerName a class name
     *
     * @return array
     */
    protected function getFormattedErrors($controllerName)
    {
        $errors = $this->getErrors($controllerName);
        $formattedErrors = [];
        if (is_array($errors) && count($errors)) {
            foreach ($errors as $location => $ex2) {
                foreach ($ex2 as $key => $er) {
                    $error = unserialize($er);
                    $formattedErrors[$location][$key] = $error->getOxMessage();
                }
            }
        }

        return $formattedErrors;
    }

    /**
     * Render BaseController object.
     *
     * @param FrontendController $view view object to render
     *
     * @return string
     */
    protected function render($view)
    {
        $templateName = $view->render();
        $viewData = $view->getViewData();

        $renderer = $this->getRenderer();

        $viewData['oxEngineTemplateId'] = $view->getViewId();
        $viewData = $this->passSessionErrorsToViewData($view, $viewData);
        try {
            $output = $renderer->renderTemplate($templateName, $viewData);
        } catch (\Throwable $exception) {
            $this->rethrowShopThrowable($exception);
            $this->processTemplateRenderError($templateName, $exception);
            $viewData = $this->passSessionErrorsToViewData($view, $viewData);
            $output = $renderer->renderTemplate('message/exception', $viewData);
        }

        return $output;
    }

    /**
     * @return string[][]
     */
    protected function getErrors($currentControllerName): array
    {
        if ($this->errors === null) {
            $this->loadErrorsFromSession();
        }
        $this->releaseErrorsForController($currentControllerName);

        Registry::getSession()->setVariable('ErrorController', $this->errorControllers);
        Registry::getSession()->setVariable('Errors', $this->remainingErrors);

        return $this->errors;
    }

    protected function isDebugMode(): bool
    {
        return ContainerFacade::getParameter('oxid_esales.debug_mode');
    }

    private function createResponse($view, string $output): Response
    {
        if (Registry::getRequest()->getRequestEscapedParameter('renderPartial')) {
            return new JsonResponse([
                'errors' => $this->getFormattedErrors($view->getClassKey()),
                'content' => $output,
            ]);
        }

        return new Response(
            $output,
            Response::HTTP_OK,
            ['Content-Type' => 'text/html; charset=' . $view->getCharSet()]
        );
    }

    private function getRenderer(): TemplateRendererInterface
    {
        return ContainerFacade::get(TemplateRendererBridgeInterface::class)
            ->getTemplateRenderer();
    }

    private function loadErrorsFromSession(): void
    {
        $sessionErrors = Registry::getSession()->getVariable('Errors');
        $sessionErrorControllers = Registry::getSession()->getVariable('ErrorController');

        $this->errors = is_array($sessionErrors) ? $sessionErrors : [];
        $this->errorControllers = is_array($sessionErrorControllers) ? $sessionErrorControllers : [];
        $this->remainingErrors = $this->errors;
    }

    private function releaseErrorsForController($currentControllerName): void
    {
        if (empty($this->errorControllers)) {
            $this->remainingErrors = [];

            return;
        }

        foreach ($this->errorControllers as $errorName => $controllerName) {
            if ($controllerName == $currentControllerName) {
                unset($this->remainingErrors[$errorName], $this->errorControllers[$errorName]);
            }
        }
    }

    private function rethrowShopThrowable(\Throwable $exception): void
    {
        $candidate = $exception;
        while ($candidate !== null) {
            if ($candidate instanceof ResponseReady || $candidate instanceof StandardException) {
                throw $candidate;
            }
            $candidate = $candidate->getPrevious();
        }
    }

    private function processTemplateRenderError(string $templateName, \Throwable $rendererError): void
    {
        $displayMessage = sprintf(
            Registry::getLang()->translateString('EXCEPTION_SYSTEMCOMPONENT_TEMPLATENOTFOUND'),
            $templateName
        );
        $displayedException = oxNew(SystemComponentException::class, $displayMessage);
        $displayedException->setComponent($templateName);
        if ($this->isDebugMode()) {
            $this->errors = null;
            Registry::getUtilsView()->addErrorToDisplay($displayedException);
        }
        Registry::getLogger()->error($displayedException->getMessage(), [$rendererError]);
    }

    private function passSessionErrorsToViewData(ViewControllerInterface $view, array $viewData): array
    {
        $errors = $this->getErrors($view->getClassKey());
        if (\is_array($errors) && count($errors)) {
            Registry::getUtilsView()->passAllErrorsToView($viewData, $errors);
        }
        return $viewData;
    }

    private function isServiceController(string $classKey, string $class): bool
    {
        return isset(ContainerFacade::getParameter('oxid.view_controllers_map')[$classKey]) && ContainerFacade::has($class);
    }
}
