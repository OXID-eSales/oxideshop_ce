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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

require_once 'Constructors/ConstructorFactory.php';

/**
 * Shop constructor class for modifying shop environment during testing
 * Class ShopConstructor
 */
class ShopObjectConstructor implements ShopServiceInterface
{
    /**
     * Loads object, sets class parameters and calls function with parameters.
     * classParams can act two ways - if array('param' => 'value') is given, it sets the values to given keys
     * if array('param', 'param') is passed, values of these params are returned.
     * classParams are only returned if no function is called. Otherwise function return value is returned.
     *
     * @return mixed
     */
    public function init()
    {
        $oxConfig = oxRegistry::getConfig();

        $oConstructorFactory = new ConstructorFactory();
        $oConstructor = $oConstructorFactory->getConstructor($oxConfig->getRequestParameter("cl"));

        $oConstructor->load($oxConfig->getRequestParameter("oxid"));

        if ($oxConfig->getRequestParameter('classparams')) {
            $mResult = $oConstructor->setClassParameters( $oxConfig->getRequestParameter('classparams') );
        }

        if ($oxConfig->getRequestParameter('fnc')) {
            $mResult = $oConstructor->callFunction($oxConfig->getRequestParameter('fnc'), $oxConfig->getRequestParameter('functionparams'));
        }

        return $mResult;
    }
}
