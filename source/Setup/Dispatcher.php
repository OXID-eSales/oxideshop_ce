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

namespace OxidEsales\EshopCommunity\Setup;

/**
 * Chooses and executes controller action which must be executec to render expected view
 */
class Dispatcher extends Core
{
    /**
     * Executes current controller action
     */
    public function run()
    {
        // choosing which controller action must be executed
        $sAction = $this->_chooseCurrentAction();

        // executing action which returns name of template to render
        /** @var Controller $oController */
        $oController = $this->getInstance("Controller");

        $view = $oController->getView();
        $view->sendHeaders();
        $view->display($oController->$sAction());
    }

    /**
     * Returns name of controller action script to perform
     *
     * @return string | null
     */
    protected function _chooseCurrentAction()
    {
        /** @var Setup $oSetup */
        $oSetup = $this->getInstance("Setup");
        $iCurrStep = $oSetup->getCurrentStep();

        $sName = null;
        foreach ($oSetup->getSteps() as $sStepName => $sStepId) {
            if ($sStepId == $iCurrStep) {
                $sActionName = str_ireplace("step_", "", $sStepName);
                $sName = str_replace("_", "", $sActionName);
                break;
            }
        }

        return $sName;
    }
}
