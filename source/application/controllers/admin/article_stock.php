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
 * Admin article inventory manager.
 * Collects such information about article as stock quantity, delivery status,
 * stock message, etc; Updates information (on user submit).
 * Admin Menu: Manage Products -> Articles -> Inventory.
 * @package admin
 */
class Article_Stock extends oxAdminDetails
{
    /**
     * Loads article Inventory information, passes it to Smarty engine and
     * returns name of template file "article_stock.tpl".
     *
     * @return string
     */
    public function render()
    {
        $myConfig = $this->getConfig();

        parent::render();

        $this->_aViewData["edit"] = $oArticle = oxNew( "oxarticle");

        $soxId = $this->getEditObjectId();
        if ( $soxId != "-1" && isset( $soxId)) {

            // load object
            $oArticle->loadInLang( $this->_iEditLang, $soxId );

            // load object in other languages
            $oOtherLang = $oArticle->getAvailableInLangs();
            if (!isset($oOtherLang[$this->_iEditLang])) {
                // echo "language entry doesn't exist! using: ".key($oOtherLang);
                $oArticle->loadInLang( key($oOtherLang), $soxId );
            }

            foreach ( $oOtherLang as $id => $language) {
                $oLang= new stdClass();
                $oLang->sLangDesc = $language;
                $oLang->selected = ($id == $this->_iEditLang);
                $this->_aViewData["otherlang"][$id] =  clone $oLang;
            }


            // variant handling
            if ( $oArticle->oxarticles__oxparentid->value) {
                $oParentArticle = oxNew( "oxarticle");
                $oParentArticle->load( $oArticle->oxarticles__oxparentid->value);
                $this->_aViewData["parentarticle"] =  $oParentArticle;
                $this->_aViewData["oxparentid"] =  $oArticle->oxarticles__oxparentid->value;
            }

            if ( $myConfig->getConfigParam( 'blMallInterchangeArticles' ) ) {
                $sShopSelect = '1';
            } else {
                $sShopID = $myConfig->getShopID();
                $sShopSelect = " oxshopid =  '$sShopID' ";
            }

            $oPriceList = oxNew("oxlist");
            $oPriceList->init( 'oxbase', "oxprice2article" );
            $sQ = "select * from oxprice2article where oxartid = '$soxId' and {$sShopSelect} and (oxamount > 0 or oxamountto > 0) order by oxamount ";
            $oPriceList->selectstring( $sQ );

            $this->_aViewData["amountprices"] = $oPriceList;

        }

        return "article_stock.tpl";
    }

    /**
     * Saves article Inventori information changes.
     *
     * @return mixed
     */
    public function save()
    {
        parent::save();

        $soxId = $this->getEditObjectId();
        $aParams = oxConfig::getParameter( "editval");

            // shopid
            $sShopID = oxSession::getVar( "actshop");
            $aParams['oxarticles__oxshopid'] = $sShopID;

        $oArticle = oxNew( "oxarticle");
        $oArticle->loadInLang( $this->_iEditLang, $soxId );

        $oArticle->setLanguage( 0 );

        // checkbox handling
        if ( !$oArticle->oxarticles__oxparentid->value && !isset( $aParams['oxarticles__oxremindactive'])) {
            $aParams['oxarticles__oxremindactive'] = 0;
        }

        $oArticle->assign( $aParams );

        //tells to article to save in different language
        $oArticle->setLanguage( $this->_iEditLang );
        $oArticle = oxRegistry::get("oxUtilsFile")->processFiles( $oArticle );

        $oArticle->resetRemindStatus();

        $oArticle->updateVariantsRemind();

        $oArticle->save();
    }

     /**
     * Adds or updates amount price to article
      *
     * @param string $sOXID         Object ID
     * @param array  $aUpdateParams Parameters
      *
     * @return null
     */
    public function addprice( $sOXID = null, $aUpdateParams = null )
    {
        $myConfig = $this->getConfig();


        $sOxArtId = $this->getEditObjectId();



        $aParams = oxConfig::getParameter( "editval" );

        if ( !is_array($aParams) ) {
            return;
        }

        if ( isset( $aUpdateParams ) && is_array( $aUpdateParams ) ) {
            $aParams = array_merge( $aParams, $aUpdateParams );
        }

        //replacing commas
        foreach ( $aParams as $key => $sParam ) {
            $aParams[$key] = str_replace( ",", ".", $sParam );
        }

        $aParams['oxprice2article__oxshopid'] = $myConfig->getShopID();

        if ( isset( $sOXID ) ) {
            $aParams['oxprice2article__oxid'] = $sOXID;
        }

        $aParams['oxprice2article__oxartid'] = $sOxArtId;
        if ( !isset( $aParams['oxprice2article__oxamount'] ) || !$aParams['oxprice2article__oxamount'] ) {
            $aParams['oxprice2article__oxamount'] = "1";
        }

        if ( !$myConfig->getConfigParam( 'blAllowUnevenAmounts' ) ) {
            $aParams['oxprice2article__oxamount']   = round( ( string ) $aParams['oxprice2article__oxamount'] );
            $aParams['oxprice2article__oxamountto'] = round( ( string ) $aParams['oxprice2article__oxamountto'] );
        }

        $dPrice = $aParams['price'];
        $sType = $aParams['pricetype'];

        $oArticlePrice = oxNew( "oxbase" );
        $oArticlePrice->init( "oxprice2article" );
        $oArticlePrice->assign( $aParams );

        $oArticlePrice->$sType = new oxField( $dPrice );

        //validating
        if ($oArticlePrice->$sType->value &&
            $oArticlePrice->oxprice2article__oxamount->value &&
            $oArticlePrice->oxprice2article__oxamountto->value &&
            is_numeric( $oArticlePrice->$sType->value ) &&
            is_numeric( $oArticlePrice->oxprice2article__oxamount->value ) &&
            is_numeric( $oArticlePrice->oxprice2article__oxamountto->value ) &&
            $oArticlePrice->oxprice2article__oxamount->value <= $oArticlePrice->oxprice2article__oxamountto->value
            ) {
            $oArticlePrice->save();
        }

        // check if abs price is lower than base price
        $oArticle = oxNew( "oxArticle");
        $oArticle->loadInLang( $this->_iEditLang, $sOxArtId );

        if ( ( $aParams['price'] >= $oArticle->oxarticles__oxprice->value) && ($aParams['pricetype'] == 'oxprice2article__oxaddabs' )  ) {
            if ( is_null($sOXID) ) {
                $sOXID = $oArticlePrice->getId();
            }
            $this->_aViewData["errorscaleprice"][] = $sOXID;
        }

    }

    /**
     * Updates all amount prices for article at once
     *
     * @return null
     */
    public function updateprices()
    {

        $aParams = oxConfig::getParameter( "updateval" );
        if ( is_array( $aParams ) ) {
            foreach ( $aParams as $soxId => $aStockParams ) {
                $this->addprice( $soxId, $aStockParams );
            }
        }
    }



    /**
     * Adds amount price to article
     *
     * @return null
     */
    public function deleteprice()
    {

        $oDb = oxDb::getDb();
        $sPriceId = $oDb->quote( oxConfig::getParameter("priceid" ) );
        $sId = $oDb->quote( $this->getEditObjectId() );
        $oDb->execute( "delete from oxprice2article where oxid = {$sPriceId} and oxartid = {$sId}" );
    }

}
