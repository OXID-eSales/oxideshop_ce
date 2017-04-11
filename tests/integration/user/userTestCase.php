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

abstract class UserTestCase extends OxidTestCase
{

    /**
     * Password encoded with old algorithm.
     *
     * @var string
     */
    protected $_sOldEncodedPassword = '4bb11fbb0c6bf332517a7ec397e49f1c';

    /**
     * Salt generated with old algorithm.
     *
     * @var string
     */
    protected $_sOldSalt = '3262383936333839303439393466346533653733366533346137326666393632';

    /**
     * Password encoded with new algorithm.
     *
     * @var string
     */
    protected $_sNewEncodedPassword = 'b016e37ac8ec71449b475e84a941e3c39a27fb8f0710d4b47d6116ad6a6afcaa0c17006a4c01ffc67f3db95772fe001584cb4ce7e5bacd74198c24d1851841d5';

    /**
     * Salt generated with new algorithm.
     *
     * @var string
     */
    protected $_sNewSalt = '56784f8ffc657fff84915b93e12a626e';

    /** @var string */
    protected $_sDefaultUserName = '_testUserName@oxid-esales.com';

    /** @var string */
    protected $_sDefaultUserPassword = '_testPassword';

    /** @var bool */
    protected $_blSkipCustomTearDown = false;

    public function tearDown()
    {
        if (!$this->_blSkipCustomTearDown) {
            $oDbRestore = $this->_getDbRestore();
            $oDbRestore->restoreTable('oxuser');
            $oDbRestore->restoreTable('oxshops');
        }
        parent::tearDown();
    }

    /**
     * Creates user with the default credentials for given shop.
     *
     * MD5 encoded password style is used for legacy shops
     *
     * @param string $sRight            OXRIGHTS column value ('malladmin', 'user' or <shopid>)
     * @param int    $iShopId           Shop ID
     * @param bool   $blMd5EncodedStyle Use MD5 encoded (legacy) password encryption
     *
     * @return oxUser
     */
    protected function _createDefaultUser($sRight, $iShopId, $blMd5EncodedStyle = true)
    {
        if ($blMd5EncodedStyle) {
            $sPassVal = $this->_sOldEncodedPassword;
            $sSaltVal = $this->_sOldSalt;
        } else {
            $sPassVal = $this->_sNewEncodedPassword;
            $sSaltVal = $this->_sNewSalt;
        }

        $oUser = new oxUser();
        $oUser->oxuser__oxusername = new oxField('_testUserName@oxid-esales.com', oxField::T_RAW);
        $oUser->oxuser__oxpassword = new oxField($sPassVal, oxField::T_RAW);
        $oUser->oxuser__oxpasssalt = new oxField($sSaltVal, oxField::T_RAW);
        $oUser->save();

        $oUserFromBase = new oxBase();
        $oUserFromBase->init('oxuser');
        $oUserFromBase->load($oUser->getId());
        $oUserFromBase->oxuser__oxshopid = new oxField($iShopId, oxField::T_RAW);
        $oUserFromBase->oxuser__oxrights = new oxField($sRight, oxField::T_RAW);
        $oUserFromBase->save();

        return $oUser;
    }

    protected function _createSecondSubShop()
    {
        $oShop = new oxShop();
        $oShop->save();
    }

    /**
     * @param string $sUserName
     * @param string $sUserPassword
     */
    protected function _login($sUserName = null, $sUserPassword = null)
    {
        if (is_null($sUserName)) {
            $sUserName = $this->_sDefaultUserName;
        }
        if (is_null($sUserPassword)) {
            $sUserPassword = $this->_sDefaultUserPassword;
        }
        $this->_setLoginParametersToRequest($sUserName, $sUserPassword);
        $oCmpUser = new oxcmp_user();
        $oCmpUser->login();
    }

    /**
     * @param string $sUserName
     * @param string $sUserPassword
     */
    private function _setLoginParametersToRequest($sUserName, $sUserPassword)
    {
        $this->setRequestParam('lgn_usr', $sUserName);
        $this->setRequestParam('lgn_pwd', $sUserPassword);
    }
}