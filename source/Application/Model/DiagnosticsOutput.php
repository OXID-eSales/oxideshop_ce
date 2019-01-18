<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

/**
 * Diagnostic tool result outputer
 * Performs OutputKey check of shop files and generates report file.
 *
 */
class DiagnosticsOutput
{
    /**
     * result key
     *
     * @var string
     */
    protected $_sOutputKey = "diagnostic_tool_result";


    /**
     * Result file path
     *
     * @var string
     */
    protected $_sOutputFileName = "diagnostic_tool_result.html";

    /**
     * Utils object
     *
     * @var mixed
     */
    protected $_oUtils = null;

    /**
     * Object constructor
     */
    public function __construct()
    {
        $this->_oUtils = \OxidEsales\Eshop\Core\Registry::getUtils();
    }

    /**
     * OutputKey setter
     *
     * @param string $sOutputKey Output key.
     */
    public function setOutputKey($sOutputKey)
    {
        if (!empty($sOutputKey)) {
            $this->_sOutputKey = $sOutputKey;
        }
    }

    /**
     * OutputKey getter
     *
     * @return string
     */
    public function getOutputKey()
    {
        return $this->_sOutputKey;
    }

    /**
     * OutputFileName setter
     *
     * @param string $sOutputFileName Output file name.
     */
    public function setOutputFileName($sOutputFileName)
    {
        if (!empty($sOutputFileName)) {
            $this->_sOutputFileName = $sOutputFileName;
        }
    }

    /**
     * OutputKey getter
     *
     * @return string
     */
    public function getOutputFileName()
    {
        return $this->_sOutputFileName;
    }

    /**
     * Stores result file in file cache
     *
     * @param string $sResult Result.
     */
    public function storeResult($sResult)
    {
        $this->_oUtils->toFileCache($this->_sOutputKey, $sResult);
    }

    /**
     * Reads exported result file contents
     *
     * @param string $sOutputKey Output key.
     *
     * @return string
     */
    public function readResultFile($sOutputKey = null)
    {
        $sCurrentKey = (empty($sOutputKey)) ? $this->_sOutputKey : $sOutputKey;

        return $this->_oUtils->fromFileCache($sCurrentKey);
    }

    /**
     * Sends generated file for download
     *
     * @param string $sOutputKey Output key.
     */
    public function downloadResultFile($sOutputKey = null)
    {
        $sCurrentKey = (empty($sOutputKey)) ? $this->_sOutputKey : $sOutputKey;

        $this->_oUtils = \OxidEsales\Eshop\Core\Registry::getUtils();
        $iFileSize = filesize($this->_oUtils->getCacheFilePath($sCurrentKey));

        $this->_oUtils->setHeader("Pragma: public");
        $this->_oUtils->setHeader("Expires: 0");
        $this->_oUtils->setHeader("Cache-Control: must-revalidate, post-check=0, pre-check=0, private");
        $this->_oUtils->setHeader('Content-Disposition: attachment;filename=' . $this->_sOutputFileName);
        $this->_oUtils->setHeader("Content-Type:text/html;charset=utf-8");
        if ($iFileSize) {
            $this->_oUtils->setHeader("Content-Length: " . $iFileSize);
        }
        echo $this->_oUtils->fromFileCache($sCurrentKey);
    }
}
