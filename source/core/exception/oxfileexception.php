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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

/**
 * exception for invalid or non existin external files, e.g.:
 * - file does not exist
 * - file is not valid xml
 */
class oxFileException extends oxException
{
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
     *
     * @return null
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
     *
     * @return null
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
        return __CLASS__.'-'.parent::getString()." Faulty File --> ".$this->_sErrFileName."\n". "Error Code --> ".$this->_sFileError;
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
