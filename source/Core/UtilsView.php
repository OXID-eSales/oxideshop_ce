<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

use OxidEsales\Eshop\Core\Contract\IDisplayError;
use OxidEsales\Eshop\Core\Exception\StandardException;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;

class UtilsView extends \OxidEsales\Eshop\Core\Base
{
    /**
     * Templating instance getter
     *
     * @return TemplateRendererInterface
     */
    private function getRenderer()
    {
        return ContainerFacade::get(TemplateRendererBridgeInterface::class)
            ->getTemplateRenderer();
    }

    /**
     * Returns rendered template output. According to debug configuration outputs
     * debug information.
     *
     * @param string $templateName template file name
     * @param object $oObject      object, witch template we wish to output
     *
     * @return string
     */
    public function getTemplateOutput($templateName, $oObject)
    {
        $debugMode = Registry::getConfig()->getConfigParam('iDebug');

        // assign
        $viewData = $oObject->getViewData();
        if (is_array($viewData)) {
            foreach (array_keys($viewData) as $viewName) {
                // show debug information
                if ($debugMode == 4) {
                    echo("TemplateData[$viewName] : \n");
                    var_export($viewData[$viewName]);
                }
            }
        } else {
            $viewData = [];
        }

        return $this->getRenderer()->renderTemplate($templateName, $viewData);
    }

    /**
     * adds the given errors to the view array
     *
     * @param array $aView  view data array
     * @param array $errors array of errors to pass to view
     */
    public function passAllErrorsToView(&$aView, $errors)
    {
        if (count($errors) > 0) {
            foreach ($errors as $sLocation => $aEx2) {
                foreach ($aEx2 as $sKey => $oEr) {
                    $aView['Errors'][$sLocation][$sKey] = unserialize($oEr);
                }
            }
        }
    }

    /**
     * Adds an exception to the array of displayed exceptions for the view
     * by default is displayed in the inc_header, but with the custom destination set to true
     * the exception won't be displayed by default but can be displayed where ever wanted in the tpl
     *
     * @param StandardException|IDisplayError|string $exception            an exception object or just a language local (string),
     *                                                                     which will be converted into a oxExceptionToDisplay object
     * @param bool                                   $blFull               if true the whole object is add to display (default false)
     * @param bool                                   $useCustomDestination true if the exception shouldn't be displayed
     *                                                                     at the default position (default false)
     * @param string                                 $customDestination    defines a name of the view variable containing
     *                                                                     the messages, overrides Parameter 'CustomError' ("default")
     * @param string                                 $activeController     defines a name of the controller, which should
     *                                                                     handle the error.
     */
    public function addErrorToDisplay($exception, $blFull = false, $useCustomDestination = false, $customDestination = "", $activeController = "")
    {
        //default
        $destination = 'default';
        $customDestination = $customDestination ? $customDestination : Registry::getRequest()->getRequestEscapedParameter('CustomError');
        if ($useCustomDestination && $customDestination) {
            $destination = $customDestination;
        }

        //starting session if not yet started as all exception
        //messages are stored in session
        $session = Registry::getSession();
        if (!$session->getId() && !$session->isHeaderSent()) {
            $session->setForceNewSession();
            $session->start();
        }

        $sessionErrors = Registry::getSession()->getVariable('Errors');
        if ($exception instanceof \OxidEsales\Eshop\Core\Exception\StandardException) {
            $exceptionToDisplay = oxNew(\OxidEsales\Eshop\Core\Exception\ExceptionToDisplay::class);
            $exceptionToDisplay->setMessage($exception->getMessage());
            $exceptionToDisplay->setExceptionType($exception->getType());

            if ($exception instanceof \OxidEsales\Eshop\Core\Exception\SystemComponentException) {
                $exceptionToDisplay->setMessageArgs($exception->getComponent());
            }

            $exceptionToDisplay->setValues($exception->getValues());
            $exceptionToDisplay->setStackTrace($exception->getTraceAsString());
            $exceptionToDisplay->setDebug($blFull);
            $exception = $exceptionToDisplay;
        } elseif ($exception instanceof \Throwable) {
            $tempException = $exception;
            $exception = oxNew(\OxidEsales\Eshop\Core\DisplayError::class);
            $exception->setMessage($tempException->getMessage());
        } elseif ($exception && !($exception instanceof \OxidEsales\Eshop\Core\Contract\IDisplayError)) {
            $tempException = $exception;
            $exception = oxNew(\OxidEsales\Eshop\Core\DisplayError::class);
            $exception->setMessage($tempException);
        } elseif ($exception instanceof \OxidEsales\Eshop\Core\Contract\IDisplayError) {
            // take the object
        } else {
            $exception = null;
        }

        if ($exception) {
            $sessionErrors[$destination][] = serialize($exception);
            Registry::getSession()->setVariable('Errors', $sessionErrors);

            if ($activeController == '') {
                $activeController = Registry::getRequest()->getRequestEscapedParameter('actcontrol');
            }
            if ($activeController) {
                $aControllerErrors[$destination] = $activeController;
                Registry::getSession()->setVariable('ErrorController', $aControllerErrors);
            }
        }
    }
}
