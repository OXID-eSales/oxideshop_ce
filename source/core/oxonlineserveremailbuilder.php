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
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
 */

/**
 * Class oxOnlineServerEmailBuilder is responsible for email sending when it's not possible to make call via CURL.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://wiki.oxidforge.org/Tutorials/Core_OXID_eShop_classes:_must_not_be_extended
 *
 * @ignore   This class will not be included in documentation.
 */
class oxOnlineServerEmailBuilder
{

    /**
     * Created oxEmail object and sets values.
     *
     * @param string $sBody Email body in XML format.
     *
     * @return oxEmail
     */
    public function build($sBody)
    {
        /** @var oxEmail $oExpirationEmail */
        $oExpirationEmail = oxNew('oxEmail');
        $oExpirationEmail->setSubject(oxRegistry::getLang()->translateString('SUBJECT_UNABLE_TO_SEND_VIA_CURL', null, true));
        $oExpirationEmail->setRecipient('olc@oxid-esales.com');
        $oExpirationEmail->setFrom($this->_getShopInfoAddress());
        $oExpirationEmail->setBody($sBody);

        return $oExpirationEmail;
    }

    /**
     * Returns active shop info email address.
     *
     * @return string
     */
    private function _getShopInfoAddress()
    {
        $oShop = oxRegistry::getConfig()->getActiveShop();

        return $oShop->oxshops__oxinfoemail->value;
    }
}
