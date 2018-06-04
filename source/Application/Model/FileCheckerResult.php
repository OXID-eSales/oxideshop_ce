<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

/**
 * File checker result class
 * Structures and keeps the result of shop file check diagnostics
 *
 * @deprecated since v6.3 (2018-06-04); This functionality will be removed completely.
 */
class FileCheckerResult
{
    /**
     * For result output
     *
     * @var mixed
     */
    protected $_aResult = [];

    /**
     * Counts number of matches for each type of result
     *
     * @var array
     */
    protected $_aResultSummary = [];

    /**
     * If the variable is true, the script will show all files, even they are ok.
     *
     * @var bool
     */
    protected $_blListAllFiles = false;

    /**
     * Object constructor
     */
    public function __construct()
    {
        $this->_aResultSummary['OK'] = 0;
        $this->_aResultSummary['VERSIONMISMATCH'] = 0;
        $this->_aResultSummary['UNKNOWN'] = 0;
        $this->_aResultSummary['MODIFIED'] = 0;
        $this->_aResultSummary['FILES'] = 0;
        $this->_aResultSummary['SHOP_OK'] = true;
    }

    /**
     * Setter for working directory
     *
     * @param boolean $blListAllFiles Whether to list all files
     */
    public function setListAllFiles($blListAllFiles)
    {
        $this->_blListAllFiles = $blListAllFiles;
    }

    /**
     * working directory getter
     *
     * @return boolean
     */
    public function getListAllFiles()
    {
        return $this->_blListAllFiles;
    }

    /**
     * Getter for file checker result
     *
     * @return array
     */
    public function getResult()
    {
        return $this->_aResult;
    }

    /**
     * Getter for file checker result summary
     *
     * @return array
     */
    public function getResultSummary()
    {
        return $this->_aResultSummary;
    }

    /**
     * Methods saves result of one file check and returns updated summary array
     *
     * @param array $aResult Result
     *
     * @return array
     */
    public function addResult($aResult)
    {
        $this->_aResultSummary['FILES']++;
        $this->_aResultSummary[$aResult['result']]++;

        if (!$aResult['ok']) {
            $this->_aResultSummary['SHOP_OK'] = false;
        }

        if (($aResult['ok'] && $this->getListAllFiles()) || !$aResult['ok']) {
            $this->_aResult[] = $aResult;
        }

        return $this->_aResultSummary;
    }
}
