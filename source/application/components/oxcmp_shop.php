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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

/**
 * Translarent shop manager (executed automatically), sets
 * registration information and current shop object.
 * @subpackage oxcmp
 */
class oxcmp_shop extends oxView
{
    /**
     * Marking object as component
     * @var bool
     */
    protected $_blIsComponent = true;

    /**
     * Executes parent::render() and returns active shop object.
     *
     * @return  object  $this->oActShop active shop object
     */
    public function render()
    {
        parent::render();

        $myConfig = $this->getConfig();

        // is shop active?
        $oShop = $myConfig->getActiveShop();
        if ( !$oShop->oxshops__oxactive->value && 'oxstart' != $myConfig->getActiveView()->getClassName() && !$this->isAdmin() ) {
            // redirect to offline if there is no active shop
            $sShopUrl = oxRegistry::getConfig()->getSslShopUrl();
            oxRegistry::getUtils()->redirect( $sShopUrl . 'offline.html', false );
        }

        return $oShop;
    }
}
