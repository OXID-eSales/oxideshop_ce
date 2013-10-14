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
 * @package   core
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id: OxSystemEventHandler
 */

/**
 * Contains system event handler methods
 */
class OxSystemEventHandler
{
    /**
     * @Var oxOnlineLicenseCheck
     */
    private $_oOLC = null;

    /**
     * OLC dependency setter
     *
     * @param oxOnlineLicenseCheck $oOLC
     */
    public function setOLC($oOLC)
    {
        $this->_oOLC = $oOLC;
    }

    /**
     * onAdminLogin() is called on every successful login to the backend
     *
     * @param string $sActiveShop Active shop
     */
    public function onAdminLogin( $sActiveShop )
    {

        //Checks if newer versions of modules are available.
        //Will be used by the upcoming online one click installer.
        //Is still under development - still changes at the remote server are necessary - therefore ignoring the results for now
        try {
            $oOMVN = oxNew("oxOnlineModuleVersionNotifier");
            $oOMVN->versionNotify();
        } catch (Exception $o) { }
    }
}
