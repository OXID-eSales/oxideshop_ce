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
 * config to fetch paths
 */
$myConfig = oxRegistry::getConfig();

/**
 * needed libraries location
 */
$sIncPath = $myConfig->getConfigParam( 'sShopDir' );

/**
 * switching cache off
 */
DEFINE( 'USE_CACHE', false );
DEFINE( 'CACHE_DIR', $myConfig->getConfigParam( 'sCompileDir' ) );

/**
 * including libraries
 */
require_once "$sIncPath/core/jpgraph/jpgraph.php";
require_once "$sIncPath/core/jpgraph/jpgraph_bar.php";
require_once "$sIncPath/core/jpgraph/jpgraph_line.php";
require_once "$sIncPath/core/jpgraph/jpgraph_pie.php";
require_once "$sIncPath/core/jpgraph/jpgraph_pie3d.php";

if ( !class_exists( 'report_base' ) ) {
/**
 * Base reports class
 * @package admin
 */
class Report_base extends oxAdminView
{
    /**
     * Smarty object
     * @return
     */
    protected $_oSmarty = null;

    /**
     * Returns name of template to render
     *
     * @return string
     */
    public function render()
    {
        return $this->_sThisTemplate;
    }

    /**
     * Smarty object setter
     *
     * @param smarty $oSmarty smarty object
     *
     * @return null
     */
    public function setSmarty( $oSmarty )
    {
        $this->_oSmarty = $oSmarty;
    }

    /**
     * Returns Smarty object
     *
     * @return smarty
     */
    public function getSmarty()
    {
        return $this->_oSmarty;
    }

    /**
     * Returns array with week range points
     *
     * @return array
     */
    public function getWeekRange()
    {
        $myConfig = $this->getConfig();

        // initializing one week before current..
        $iFrom = oxRegistry::get("oxUtilsDate")->getWeekNumber($myConfig->getConfigParam( 'iFirstWeekDay' ), strtotime( oxConfig::getParameter( "time_from") ) );
        $iTo   = oxRegistry::get("oxUtilsDate")->getWeekNumber($myConfig->getConfigParam( 'iFirstWeekDay' ), strtotime( oxConfig::getParameter( "time_to") ) );

        return array( $iFrom - 1, $iTo + 1 );
    }

    /**
     * Returns predefined graph object
     *
     * @param int    $iXSize         graph image x size
     * @param int    $iYSize         graph image y size
     * @param string $sBackgroundImg background filler image (full path) [oxConfig::getImageDir( true ) ."/reportbgrnd.jpg"]
     * @param string $sScaleType     graph scale type ["textlin"]
     *
     * @return Graph
     */
    public function getGraph( $iXSize, $iYSize, $sBackgroundImg = null, $sScaleType = "textlin" )
    {
        $sBackgroundImg = $sBackgroundImg ? $sBackgroundImg : $this->getConfig()->getImageDir( true )."/reportbgrnd.jpg";

        // New graph with a drop shadow
        $oGraph = new Graph( $iXSize, $iYSize );

        $oGraph->setBackgroundImage( $sBackgroundImg, BGIMG_FILLFRAME );

        // Use a "text" X-scale
        $oGraph->setScale( $sScaleType );

        // Label align for X-axis
        $oGraph->xaxis->setLabelAlign( 'center', 'top', 'right' );

        // Label align for Y-axis
        $oGraph->yaxis->setLabelAlign( 'right', 'bottom' );

        // shadow
        $oGraph->setShadow();

        // Use built in font
        $oGraph->title->setFont( FF_FONT1, FS_BOLD );

        return $oGraph;
    }
}
}