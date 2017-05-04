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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

/**
 * Class used for uploading files in services.
 */
class FileUploader
{
    /**
     * Uploads file to given location.
     *
     * @param string $sFileIndex file index
     * @param string $sLocation  location where to put uploaded file
     * @param bool $blOverwrite  whether to overwrite existing file
     * @throws Exception         throws exception if file with given index does not exist.
     * @return bool              whether upload succeeded
     */
    public function uploadFile($sFileIndex, $sLocation, $blOverwrite = true)
    {
        $aFileInfo = $this->_getFileInfo($sFileIndex);

        if (!$this->_checkFile($aFileInfo)) {
            throw new Exception("File with index '$sFileIndex' does not exist or error occurred while downloading it");
        }

        return $this->_moveUploadedFile($aFileInfo, $sLocation, $blOverwrite);
    }

    /**
     * @param $aFileInfo
     * @return bool
     */
    private function _checkFile($aFileInfo)
    {
        $blResult = isset($aFileInfo['name']) && isset($aFileInfo['tmp_name']);

        if ($blResult && isset($aFileInfo['error']) && $aFileInfo['error']) {
            $blResult = false;
        }

        return $blResult;
    }

    /**
     * @param $sFileIndex
     * @return null
     */
    private function _getFileInfo($sFileIndex)
    {
        return $_FILES[$sFileIndex];
    }

    /**
     * @param $aFileInfo
     * @param $sLocation
     * @param $blOverwrite
     * @return bool
     */
    private function _moveUploadedFile($aFileInfo, $sLocation, $blOverwrite)
    {
        $blDone = false;

        if (!file_exists($sLocation) || $blOverwrite) {
            $blDone = move_uploaded_file($aFileInfo['tmp_name'], $sLocation);

            if ($blDone) {
                $blDone = @chmod($sLocation, 0644);
            }
        }

        return $blDone;
    }
}