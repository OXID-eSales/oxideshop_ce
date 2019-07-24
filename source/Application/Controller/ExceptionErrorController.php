<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller;

/**
 * Displays exception errors
 */
class ExceptionErrorController extends \OxidEsales\Eshop\Application\Controller\FrontendController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'message/exception.tpl';

    /** @var array Remove loading of components on exception handling. */
    protected $_aComponentNames = [];

    /**
     * Sets exception errros to template
     */
    public function displayExceptionError()
    {
        $aViewData = $this->getViewData();

        //add all exceptions to display
        $aErrors = $this->_getErrors();

        if (is_array($aErrors) && count($aErrors)) {
            \OxidEsales\Eshop\Core\Registry::getUtilsView()->passAllErrorsToView($aViewData, $aErrors);
        }

        $this->addTplParam('Errors', $aViewData['Errors']);

        // resetting errors from session
        \OxidEsales\Eshop\Core\Registry::getSession()->setVariable('Errors', []);
    }

    /**
     * return page errors array
     *
     * @return array
     */
    protected function _getErrors()
    {
        $aErrors = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable('Errors');

        if (null === $aErrors) {
            $aErrors = [];
        }

        return $aErrors;
    }
}
