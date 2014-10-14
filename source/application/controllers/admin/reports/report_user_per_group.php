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

if ( !class_exists( "report_user_per_group")) {
/**
 * User per group reports class
 * @package admin
 */
class Report_user_per_group extends report_base
{
    /**
     * Name of template to render
     *
     * @return string
     */
    protected $_sThisTemplate = "report_user_per_group.tpl";

    /**
     * Checks if db contains data for report generation
     *
     * @return bool
     */
    public function drawReport()
    {
        $sQ = "SELECT 1 FROM oxobject2group, oxuser, oxgroups
               WHERE oxobject2group.oxobjectid = oxuser.oxid AND
               oxobject2group.oxgroupsid = oxgroups.oxid";
        return oxDb::getDb()->getOne( $sQ );
    }

    /**
     * Collects and renders user per group report data
     *
     * @return null
     */
    public function user_per_group()
    {
        $myConfig = $this->getConfig();
        $oDb = oxDb::getDb();

        global $aTitles;

        $aDataX = array();
        $aDataY = array();

        $sSQL = "SELECT oxgroups.oxtitle,
                        count(oxuser.oxid)
                 FROM oxobject2group,
                      oxuser,
                      oxgroups
                 WHERE oxobject2group.oxobjectid = oxuser.oxid  AND
                       oxobject2group.oxgroupsid = oxgroups.oxid
                 GROUP BY oxobject2group.oxgroupsid
                 ORDER BY oxobject2group.oxgroupsid";

        $rs = $oDb->execute( $sSQL);
        if ($rs != false && $rs->recordCount() > 0) {
            while (!$rs->EOF) {
                if ( $rs->fields[1]) {
                    $aDataX[] = $rs->fields[1];
                    $aDataY[] = $rs->fields[0];
                }
                $rs->moveNext();
            }
        }

        header ("Content-type: image/png" );

        // New graph with a drop shadow
        if (count($aDataX) > 10)
            $graph = new PieGraph(800, 830);
        else
            $graph = new PieGraph(600, 600);

        $graph->setBackgroundImage( $myConfig->getImageDir(true)."/reportbgrnd.jpg", BGIMG_FILLFRAME);
        $graph->setShadow();

        // Set title and subtitle
        //$graph->title->set($this->aTitles[$myConfig->getConfigParam( 'iAdminLanguage' ) ]);
        $graph->title->set($this->aTitles[oxRegistry::getLang()->getObjectTplLanguage() ]);

        // Use built in font
        $graph->title->setFont(FF_FONT1, FS_BOLD);

        // Create the bar plot
        $bplot = new PiePlot3D($aDataX);

        $bplot->setSize(0.4);
        $bplot->setCenter(0.5, 0.32);

        // explodes all chunks of Pie from center point
        $bplot->explodeAll(10);
        $iUserCount = 0;
        foreach ($aDataX as $iVal)
            $iUserCount += $iVal;
        for ($iCtr = 0; $iCtr < count($aDataX); $iCtr++) {
            $iSLeng = strlen($aDataY[$iCtr]);
            if ($iSLeng > 20) {
                if ($iSLeng > 23)
                    $aDataY[$iCtr] = trim(substr($aDataY[$iCtr], 0, 20))."...";

            }
            $aDataY[$iCtr] .= " - ".$aDataX[$iCtr]." Kund.";
        }
        $bplot->setLegends($aDataY);

        if (count($aDataX) > 10) {
            $graph->legend->pos(0.49, 0.66, 'center');
            $graph->legend->setFont(FF_FONT0, FS_NORMAL);
            $graph->legend->setColumns(4);
        } else {
            $graph->legend->pos(0.49, 0.70, 'center');
            $graph->legend->setFont(FF_FONT1, FS_NORMAL);
            $graph->legend->setColumns(2);
        }

        $graph->add($bplot);

        // Finally output the  image
        $graph->stroke();
    }
}
}