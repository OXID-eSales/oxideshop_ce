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
 * Contains system event handler methods
 */
class OxSystemEventHandler
{
    /**
     * @Var oxOnlineModuleVersionNotifier
     */
    private $_oOMVN = null;


    /**
     * OMVN dependency setter
     *
     * @param oxOnlineModuleVersionNotifier $oOLC
     */
    public function setOMVN($oOMVN)
    {
        $this->_oOMVN = $oOMVN;
    }

    /**
     * OMVN dependency getter
     *
     * @return oxOnlineModuleVersionNotifier
     */
    public function getOMVN()
    {
        if (!$this->_oOMVN) {
            $oOMVN = oxNew("oxOnlineModuleVersionNotifier");
            $this->setOMVN( $oOMVN );
        }

        return $this->_oOMVN;
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
            $this->getOMVN()->versionNotify();
        } catch (Exception $o) { }
    }
}
