<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * class for output processing
 */
class Output extends \OxidEsales\Eshop\Core\Base
{
    const OUTPUT_FORMAT_HTML = 'html';
    const OUTPUT_FORMAT_JSON = 'json';

    /**
     * Keels search engine status
     *
     * @var bool
     */
    protected $_blSearchEngine = false;

    /**
     * page charset
     *
     * @var string
     */
    protected $_sCharset = null;

    /**
     * output format (html(default)/json)
     *
     * @var string
     */
    protected $_sOutputFormat = self::OUTPUT_FORMAT_HTML;

    /**
     * output buffer (e.g. for json)
     *
     * @var array
     */
    protected $_aBuffer = [];

    /**
     * Class constructor. Sets search engine mode according to client info
     *
     * @return null
     */
    public function __construct()
    {
        $this->setIsSearchEngine(\OxidEsales\Eshop\Core\Registry::getUtils()->isSearchEngine());
    }

    /**
     * Search engine mode setter
     *
     * @param bool $blOn search engine mode
     */
    public function setIsSearchEngine($blOn)
    {
        $this->_blSearchEngine = $blOn;
    }

    /**
     * function for front-end (normaly HTML) output processing
     * This function is called from index.php
     *
     * @param string $sValue     value
     * @param string $sClassName classname
     *
     * @return string
     */
    public function process($sValue, $sClassName)
    {
        return $sValue;
    }

    /**
     * Add a version tag to a html page
     *
     * @param string $sOutput htmlheader
     *
     * @return string
     */
    final public function addVersionTags($sOutput)
    {
        // DISPLAY IT
        $sVersion = $this->getConfig()->getVersion();
        $sEdition = $this->getConfig()->getFullEdition();
        $sCurYear = date("Y");

        // SHOW ONLY MAJOR VERSION NUMBER
        $aVersion = explode('.', $sVersion);
        $sMajorVersion = reset($aVersion);

        $sShopMode = $this->getShopMode();

        // Replacing only once per page
        $sSearch = "</head>";
        $sReplace = "</head>\n  <!-- OXID eShop {$sEdition}, Version {$sMajorVersion}{$sShopMode}, Shopping Cart System (c) OXID eSales AG 2003 - {$sCurYear} - https://www.oxid-esales.com -->";

        $sOutput = ltrim($sOutput);
        if (($pos = stripos($sOutput, $sSearch)) !== false) {
            $sOutput = substr_replace($sOutput, $sReplace, $pos, strlen($sSearch));
        }

        return $sOutput;
    }

    /**
     * Abstract function for smarty tag processing
     * This function is called from index.php
     *
     * @param array  $aViewData  viewarray
     * @param string $sClassName classname
     *
     * @return array
     */
    public function processViewArray($aViewData, $sClassName)
    {
        return $aViewData;
    }

    /**
     * This function is called from index.php
     *
     * @param object $oEmail email object
     */
    public function processEmail(&$oEmail)
    {
        // #669 PHP5 claims that you cant pas full this but should instead pass reference what is anyway a much better idea
        // removed "return" as by reference you dont need any return
    }


    /**
     * set page charset
     *
     * @param string $sCharset charset to send with headers
     */
    public function setCharset($sCharset)
    {
        $this->_sCharset = $sCharset;
    }

    /**
     * set page output format
     *
     * @param string $sFormat html or json
     */
    public function setOutputFormat($sFormat)
    {
        $this->_sOutputFormat = $sFormat;
    }

    /**
     * output data
     *
     * @param string $sName  output name (used in json mode)
     * @param string $output output text/data
     */
    public function output($sName, $output)
    {
        switch ($this->_sOutputFormat) {
            case self::OUTPUT_FORMAT_JSON:
                $this->_aBuffer[$sName] = $output;
                break;
            case self::OUTPUT_FORMAT_HTML:
            default:
                echo $output;
                break;
        }
    }

    /**
     * flush pending output
     */
    public function flushOutput()
    {
        switch ($this->_sOutputFormat) {
            case self::OUTPUT_FORMAT_JSON:
                echo getStr()->jsonEncode($this->_aBuffer);
                break;
            case self::OUTPUT_FORMAT_HTML:
            default:
                break;
        }
    }

    /**
     * send page headers (content type, charset)
     */
    public function sendHeaders()
    {
        switch ($this->_sOutputFormat) {
            case self::OUTPUT_FORMAT_JSON:
                \OxidEsales\Eshop\Core\Registry::getUtils()->setHeader("Content-Type: application/json; charset=" . $this->_sCharset);
                break;
            case self::OUTPUT_FORMAT_HTML:
            default:
                \OxidEsales\Eshop\Core\Registry::getUtils()->setHeader("Content-Type: text/html; charset=" . $this->_sCharset);
                break;
        }
    }

    /**
     * Forms Shop mode name.
     *
     * @return string
     */
    protected function getShopMode()
    {
        return '';
    }
}
