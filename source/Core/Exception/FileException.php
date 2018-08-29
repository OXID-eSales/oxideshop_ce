<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Exception;

/**
 * exception for invalid or non existin external files, e.g.:
 * - file does not exist
 * - file is not valid xml
 */
class FileException extends \OxidEsales\Eshop\Core\Exception\StandardException
{
    /**
     * Exception type, currently old class name is used.
     *
     * @var string
     */
    protected $type = 'oxFileException';

    /**
     * File connected to this exception.
     *
     * @var string
     */
    protected $_sErrFileName;

    /**
     * Error occured with the file, if provided
     *
     * @var string
     */
    protected $_sFileError;

    /**
     *  Sets the file name of the file related to the exception
     *
     * @param string $sFileName file name
     */
    public function setFileName($sFileName)
    {
        $this->_sErrFileName = $sFileName;
    }

    /**
     * Gives file name related to the exception
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->_sErrFileName;
    }

    /**
     * sets the error returned by the file operation
     *
     * @param string $sFileError Error
     */
    public function setFileError($sFileError)
    {
        $this->_sFileError = $sFileError;
    }

    /**
     * return the file error
     *
     * @return string
     */
    public function getFileError()
    {
        return $this->_sFileError;
    }

    /**
     * Get string dump
     * Overrides oxException::getString()
     *
     * @return string
     */
    public function getString()
    {
        return __CLASS__ . '-' . parent::getString() . " Faulty File --> " . $this->_sErrFileName . "\n" . "Error Code --> " . $this->_sFileError;
    }

    /**
     * Override of oxException::getValues()
     *
     * @return array
     */
    public function getValues()
    {
        $aRes = parent::getValues();
        $aRes['fileName'] = $this->getFileName();

        return $aRes;
    }
}
