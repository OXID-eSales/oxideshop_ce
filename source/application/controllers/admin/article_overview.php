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
 * Admin article overview manager.
 * Collects and previews such article information as article creation date,
 * last modification date, sales rating and etc.
 * Admin Menu: Manage Products -> Articles -> Overview.
 * @package admin
 */
class Article_Overview extends oxAdminDetails
{
    /**
     * Loads article overview data, passes to Smarty engine and returns name
     * of template file "article_overview.tpl".
     *
     * @return string
     */
    public function render()
    {
        $myConfig = $this->getConfig();

        parent::render();

        $this->_aViewData['edit'] = $oArticle = oxNew( 'oxarticle' );

        $soxId = $this->getEditObjectId();
        if ( $soxId != "-1" && isset( $soxId ) ) {

            // load object
            $oArticle->loadInLang( oxConfig::getParameter( "editlanguage" ), $soxId );


            $oDB = oxDb::getDb();

                // variant handling
                if ( $oArticle->oxarticles__oxparentid->value) {
                    $oParentArticle = oxNew( "oxarticle");
                    $oParentArticle->load( $oArticle->oxarticles__oxparentid->value);
                    $this->_aViewData["parentarticle"] =  $oParentArticle;
                    $this->_aViewData["oxparentid"] =  $oArticle->oxarticles__oxparentid->value;
                }

                // ordered amount
                $sSelect =  "select sum(oxamount) from oxorderarticles ";
                $sSelect .= "where oxartid=".$oDB->quote( $soxId );
                $this->_aViewData["totalordercnt"] = $iTotalOrderCnt = (float) $oDB->getOne( $sSelect);

                // sold amount
                $sSelect  = "select sum(oxorderarticles.oxamount) from  oxorderarticles, oxorder ";
                $sSelect .= "where (oxorder.oxpaid>0 or oxorder.oxsenddate > 0) and oxorderarticles.oxstorno != '1' and oxorderarticles.oxartid=".$oDB->quote( $soxId );
                $sSelect .= "and oxorder.oxid =oxorderarticles.oxorderid";
                $this->_aViewData["soldcnt"] = $iSoldCnt = (float) $oDB->getOne( $sSelect);;

                // canceled amount
                $sSelect =  "select sum(oxamount) from oxorderarticles where oxstorno = '1' and oxartid=".$oDB->quote( $soxId );
                $this->_aViewData["canceledcnt"] = $iCanceledCnt = (float) $oDB->getOne( $sSelect);

                // not yet processed
                $this->_aViewData["leftordercnt"] = $iTotalOrderCnt - $iSoldCnt - $iCanceledCnt;

                // position in top ten
                $sSelect =  "select oxartid,sum(oxamount) as cnt from oxorderarticles group by oxartid order by cnt desc";

            $rs = $oDB->execute( $sSelect );
            $iTopPos = 0;
            $iPos    = 0;
            if ( $rs != false && $rs->recordCount() > 0) {
                while (!$rs->EOF) {
                    $iPos++;
                    if( $rs->fields[0] == $soxId)
                        $iTopPos = $iPos;
                    $rs->moveNext();
                }
            }

            $this->_aViewData["postopten"]   = $iTopPos;
            $this->_aViewData["toptentotal"] = $iPos;
        }

        $this->_aViewData["afolder"]   = $myConfig->getConfigParam( 'aProductfolder' );
        $this->_aViewData["aSubclass"] = $myConfig->getConfigParam( 'aArticleClasses' );

        return "article_overview.tpl";
    }

}
