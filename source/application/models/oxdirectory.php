<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   core
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 */

/**
 * Directory reader.
 * Performs reading and checking of shop files list
 *
 * @package model
 */

class oxDirectory
{
    /**
     * base directory
     *
     * @var string
     */
    protected $_sBaseDirectory;


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
     * checks if file path exists, relative to shop base directory
     *
     * @param string $sFile
     *
     * @return boolean
     */
    public function fileExists( $sFilePath )
    {
        return is_file( $this->_sBaseDirectory . $sFilePath );
    }

    /**
     * browse all folders and sub-folders after files which have given extensions
     *
     * @param string $sFolder which is explored
     * @param array $aExtensions list of extensions to scan - if empty all files are taken
     * @param boolean $blRecursive should directories be checked in recursive manner
     * @throws exception
     * @return array list of files in given directory
     */
    public function getDirectoryFiles( $sFolder, $aExtensions = array(), $blRecursive = false )
    {
        if ( empty( $sFolder ) ) {
            throw new Exception( 'Parameter $sFolder is empty!' );
        }

        if ( empty( $this->_sBaseDirectory ) ) {
            throw new Exception( 'Base directory is not set, please use setter setBaseDirectory!' );
        }

        $aCurrentList = array();

        if (!is_dir( $this->_sBaseDirectory . $sFolder ) ) {
            return $aCurrentList;
        }

        $handle = opendir( $this->_sBaseDirectory . $sFolder );

        while ( $sFile = readdir( $handle ) )
        {

            if ( $sFile != "." && $sFile != "..")
            {

                if ( is_dir( $this->_sBaseDirectory . $sFolder . $sFile ) )
                {
                    if ( $blRecursive )
                    {
                        $aCurrentList = array_merge( $aCurrentList, $this->getDirectoryFiles( $sFolder . $sFile . '/', $aExtensions, $blRecursive ) );
                    }
                }
                else
                {
                    $sExt = substr( strrchr( $sFile, '.'), 1 );

                    if ( ( !empty( $aExtensions ) && is_array( $aExtensions ) && in_array( $sExt, $aExtensions ) ) ||
                         ( empty( $aExtensions ) ) ) {

                        if ( is_file( $this->_sBaseDirectory . $sFolder . $sFile ) ) {
                            $aCurrentList[] = $sFolder . $sFile;
                        }
                    }
                }
            }
        }
        closedir( $handle );

        return $aCurrentList;
    }

}