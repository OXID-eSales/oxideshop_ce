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
 * Hash password together with salt, using set hash algorithm
 */
class oxPasswordHasher
{

    /**
     * @var oxHasher
     */
    private $_ohasher = null;

    /**
     * Gets hasher.
     *
     * @return oxHasher
     */
    protected function _getHasher()
    {
        return $this->_ohasher;
    }

    /**
     * Sets dependencies.
     *
     * @param oxHasher $oHasher hasher.
     */
    public function __construct($oHasher)
    {
        $this->_ohasher = $oHasher;
    }

    /**
     * Hash password with a salt.
     *
     * @param string $sPassword not hashed password.
     * @param string $sSalt     salt string.
     *
     * @return string
     */
    public function hash($sPassword, $sSalt)
    {
        return $this->_getHasher()->hash($sPassword . $sSalt);
    }
}
