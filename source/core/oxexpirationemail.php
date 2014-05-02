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
 * Class oxExpirationEmail
 */
class oxExpirationEmail
{
    /**
     * Email object which is used to send an email.
     *
     * @var oxEmail
     */
    private $_oEmail;

    /**
     * Email content which can be set.
     *
     * @var string
     */
    private $_sBody;

    /**
     * If object was given in parameter it sets it and if not it creates oxEmail object and sets it.
     *
     * @param null|oxEmail $oEmail
     */
    public function __construct($oEmail = null)
    {
        if (is_null($oEmail)) {
            $oEmail = oxNew('oxEmail');
        }
        $this->_oEmail = $oEmail;
    }

    /**
     * Returns email object which was set in constructor and it used to send an email.
     *
     * @return oxEmail
     */
    public function getEmail()
    {
        return $this->_oEmail;
    }

    /**
     * Sets email content.
     *
     * @param string $sBody
     */
    public function setBody($sBody)
    {
        $this->_sBody = $sBody;
    }

    /**
     * Function returns email content.
     * If email content is not set, it's set default expiration message.
     *
     * @return string
     */
    public function getBody()
    {
        if (is_null($this->_sBody)) {
            $this->setBody(oxRegistry::getLang()->translateString('SHOP_LICENSE_ERROR_GRACE_EXPIRED', null, true));
        }

        return $this->_sBody;
    }

    /**
     * Sends expiration information to eShop info email.
     */
    public function send()
    {
        $oEmail = $this->getEmail();
        $oEmail->setFrom($this->_getInfoEmailAddress());
        $oEmail->setRecipient($this->_getInfoEmailAddress());
        $oEmail->setSubject(oxRegistry::getLang()->translateString('SHOP_LICENSE_ERROR_INFORMATION', null, true));
        $oEmail->setBody($this->getBody());
        $oEmail->send();
    }

    /**
     * Returns eShop info email address.
     *
     * @return string
     */
    private function _getInfoEmailAddress()
    {
        $oShop = oxRegistry::getConfig()->getActiveShop();

        return $oShop->oxshops__oxinfoemail->value;
    }
}