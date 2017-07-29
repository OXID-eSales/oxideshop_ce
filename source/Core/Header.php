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

namespace OxidEsales\EshopCommunity\Core;

/**
 * HTTP headers formator.
 * Collects HTTP headers and form HTTP header.
 */
class Header
{

    protected $_aHeader = array();

    /**
     * Sets header.
     *
     * @param string $header header value.
     */
    public function setHeader($header)
    {
        $header = str_replace(array("\n", "\r"), '', $header);
        $this->_aHeader[] = (string) $header . "\r\n";
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
        foreach ($this->_aHeader as $header) {
            if (isset($header)) {
                header($header);
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
        $header = "Cache-Control: no-cache;";
        $this->setHeader($header);
    }
}
