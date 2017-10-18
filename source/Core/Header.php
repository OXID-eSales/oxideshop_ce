<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * HTTP headers formator.
 * Collects HTTP headers and form HTTP header.
 */
class Header
{
    protected $_aHeader = [];

    /**
     * Sets header.
     *
     * @param string $header header value.
     */
    public function setHeader($header)
    {
        $header = str_replace(["\n", "\r"], '', $header);
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
