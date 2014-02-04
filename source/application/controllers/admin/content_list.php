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
 * Admin Contents manager.
 * Collects Content base information (Description), there is ability to filter
 * them by Description or delete them.
 * Admin Menu: Customerinformations -> Content.
 * @package admin
 */
class Content_List extends oxAdminList
{
    /**
     * Name of chosen object class (default null).
     *
     * @var string
     */
    protected $_sListClass = 'oxcontent';

    /**
     * Type of list.
     *
     * @var string
     */
    protected $_sListType = 'oxcontentlist';

    /**
     * Executes parent method parent::render() and returns name of template
     * file "Content_list.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $sFolder = oxConfig::getParameter( "folder" );
        $sFolder = $sFolder ? $sFolder : -1;

        $this->_aViewData["folder"]  = $sFolder;
        $this->_aViewData["afolder"] = $this->getConfig()->getConfigParam( 'aCMSfolder' );
        return "content_list.tpl";
    }

    /**
     * Adding folder check and empty folder field check
     *
     * @param array  $aWhere  SQL condition array
     * @param string $sqlFull SQL query string
     *
     * @return $sQ
     */
    protected function _prepareWhereQuery( $aWhere, $sqlFull )
    {
        $sQ = parent::_prepareWhereQuery( $aWhere, $sqlFull );
        $sFolder = oxConfig::getParameter( 'folder' );
        $sViewName = getviewName( "oxcontents" );

        //searchong for empty oxfolder fields
        if ( $sFolder == 'CMSFOLDER_NONE' || $sFolder == 'CMSFOLDER_NONE_RR') {
            $sQ .= " and {$sViewName}.oxfolder = '' ";
        } elseif ( $sFolder && $sFolder != '-1' ) {
            $sFolder = oxDb::getDb()->quote( $sFolder );
            $sQ .= " and {$sViewName}.oxfolder = {$sFolder}";
        }


        return $sQ;
    }

}
