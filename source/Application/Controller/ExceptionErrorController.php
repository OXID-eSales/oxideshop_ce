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

namespace OxidEsales\EshopCommunity\Application\Controller;

use oxRegistry;

/**
 * Displays exception errors
 */
class ExceptionErrorController extends \oxUBase
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'message/exception.tpl';

    /** @var array Remove loading of components on exception handling. */
    protected $_aComponentNames = array();

    /**
     * Sets exception errros to template
     */
    public function displayExceptionError()
    {
        $aViewData = $this->getViewData();

        //add all exceptions to display
        $aErrors = $this->_getErrors();

        if (is_array($aErrors) && count($aErrors)) {
            oxRegistry::get("oxUtilsView")->passAllErrorsToView($aViewData, $aErrors);
        }

        $oSmarty = oxRegistry::get("oxUtilsView")->getSmarty();
        $oSmarty->assign_by_ref("Errors", $aViewData["Errors"]);

        // resetting errors from session
        oxRegistry::getSession()->setVariable('Errors', array());
    }

    /**
     * return page errors array
     *
     * @return array
     */
    protected function _getErrors()
    {
        $aErrors = oxRegistry::getSession()->getVariable('Errors');

        if (null === $aErrors) {
            $aErrors = array();
        }

        return $aErrors;
    }
}
