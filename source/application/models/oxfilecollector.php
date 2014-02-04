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
 * Directory reader.
 * Performs reading of file list of one shop directory
 *
 * @package model
 */

class oxFileCollector
{
    /**
     * base directory
     *
     * @var string
     */
    protected $_sBaseDirectory;

    /**
     * array of collected files
     *
     * @var array
     */
    protected $_aFiles;

    /**
     * Setter for working directory
     *
     * @param $sDir
     */
    public function setBaseDirectory( $sDir )
    {
        if ( !empty( $sDir ) )
        {
            $this->_sBaseDirectory = $sDir;
        }
    }

    /**
     * get collection files
     *
     * @return mixed
     */
    public function getFiles()
    {
        return $this->_aFiles;
    }

    /**
     * Add one file to collection if it exists
     *
     * @param string $sFile file name to add to collection
     * @throws Exception
     * @return null
     */
    public function addFile( $sFile )
    {
        if ( empty( $sFile ) ) {
            throw new Exception( 'Parameter $sFile is empty!' );
        }

        if ( empty( $this->_sBaseDirectory ) ) {
            throw new Exception( 'Base directory is not set, please use setter setBaseDirectory!' );
        }

        if ( is_file( $this->_sBaseDirectory .  $sFile ) ) {

            $this->_aFiles[] = $sFile;
            return true;
        }

        return false;
    }


    /**
     * browse all folders and sub-folders after files which have given extensions
     *
     * @param string $sFolder which is explored
     * @param array $aExtensions list of extensions to scan - if empty all files are taken
     * @param boolean $blRecursive should directories be checked in recursive manner
     * @throws exception
     * @return null
     */
    public function addDirectoryFiles( $sFolder, $aExtensions = array(), $blRecursive = false )
    {
        if ( empty( $sFolder ) ) {
            throw new Exception( 'Parameter $sFolder is empty!' );
        }

        if ( empty( $this->_sBaseDirectory ) ) {
            throw new Exception( 'Base directory is not set, please use setter setBaseDirectory!' );
        }

        $aCurrentList = array();

        if (!is_dir( $this->_sBaseDirectory . $sFolder ) ) {
            return;
        }

        $handle = opendir( $this->_sBaseDirectory . $sFolder );

        while ( $sFile = readdir( $handle ) ){

            if ( $sFile != "." && $sFile != "..") {
                if ( is_dir( $this->_sBaseDirectory . $sFolder . $sFile ) ) {
                    if ( $blRecursive ) {
                        $aResultList = $this->addDirectoryFiles( $sFolder . $sFile . '/', $aExtensions, $blRecursive );

                        if ( is_array( $aResultList ) ) {
                            $aCurrentList = array_merge( $aCurrentList, $aResultList );
                        }
                    }
                }
                else
                {
                    $sExt = substr( strrchr( $sFile, '.'), 1 );

                    if ( ( !empty( $aExtensions ) && is_array( $aExtensions ) && in_array( $sExt, $aExtensions ) ) ||
                         ( empty( $aExtensions ) ) ) {

                        $this->addFile( $sFolder . $sFile );
                    }
                }
            }
        }
        closedir( $handle );
    }

}