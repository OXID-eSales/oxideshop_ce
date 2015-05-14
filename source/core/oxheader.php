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
 * HTTP headers formator.
 * Collects HTTP headers and form HTTP header.
 */
class oxHeader
{

    protected $_aHeader = array();

    /**
     * Sets header.
     *
     * @param string $sHeader header value.
     */
    public function setHeader($sHeader)
    {
        $sHeader = str_replace(array("\n", "\r"), '', $sHeader);
        $this->_aHeader[] = (string) $sHeader . "\r\n";
    }

    /**
     * Return header.
     *
     * @return array
     */
    public function getHeader()
    {
        return $this->_aHeader;
    }

    /**
     * Outputs HTTP header.
     */
    public function sendHeader()
    {
        foreach ($this->_aHeader as $sHeader) {
            if (isset($sHeader)) {
                header($sHeader);
            }
        }
    }

    /**
     * Set to not cacheable.
     *
     * @todo check browser for different no-cache signs.
     */
    public function setNonCacheable()
    {
        $sHeader = "Cache-Control: no-cache;";
        $this->setHeader($sHeader);
    }
}
