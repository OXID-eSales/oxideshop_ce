<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * simple class to add a error message to display
 */
class DisplayError implements \OxidEsales\Eshop\Core\Contract\IDisplayError
{
    /**
     * Error message
     *
     * @var string $_sMessage
     */
    protected $_sMessage;

    /** @var array */
    private $_aFormatParameters = [];

    /**
     * Formats message using vsprintf if property _aFormatParameters was set and returns translated message.
     *
     * @return string stored message
     */
    public function getOxMessage()
    {
        $translatedMessage = \OxidEsales\Eshop\Core\Registry::getLang()->translateString($this->_sMessage);
        if (!empty($this->_aFormatParameters)) {
            $translatedMessage = vsprintf($translatedMessage, $this->_aFormatParameters);
        }

        return $translatedMessage;
    }

    /**
     * Stored the message.
     *
     * @param string $message message
     */
    public function setMessage($message)
    {
        $this->_sMessage = $message;
    }

    /**
     * Stes format parameters for message.
     *
     * @param array $formatParameters
     */
    public function setFormatParameters($formatParameters)
    {
        $this->_aFormatParameters = $formatParameters;
    }

    /**
     * Returns errorrous class name (currently returns null)
     *
     * @return null
     */
    public function getErrorClassType()
    {
        return null;
    }

    /**
     * Returns value (currently returns empty string)
     *
     * @param string $name value ignored
     *
     * @return string
     */
    public function getValue($name)
    {
        return '';
    }
}
