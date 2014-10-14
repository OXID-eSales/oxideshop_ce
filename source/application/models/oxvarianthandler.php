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
 * oxVariantHandler encapsulates methods dealing with multidimensional variant and variant names.
 *
 * @package model
 */
class oxVariantHandler extends oxSuperCfg
{
    /**
     * Variant names
     *
     * @var array
     */
    protected $_oArticles = null;

    /**
     * Multidimensional variant separator
     *
     * @var string
     */
    protected $_sMdSeparator = " | ";

    /**
     * Multidimensional variant tree structure
     *
     * @var OxMdVariant
     */
    protected $_oMdVariants = null;

    /**
     * Sets internal variant name array from article list.
     *
     * @param oxList[string]oxArticle $oArticles Variant list as
     *
     * @return null
     */
    public function init( $oArticles )
    {
        $this->_oArticles = $oArticles;
    }

    /**
     * Returns multidimensional variant structure
     *
     * @param object $oVariants all article variants
     * @param string $sParentId parent article id
     *
     * @return OxMdVariants
     */
    public function buildMdVariants( $oVariants, $sParentId )
    {
        $oMdVariants = oxNew( "OxMdVariant" );
        $oMdVariants->setParentId( $sParentId );
        $oMdVariants->setName( "_parent_product_" );
        foreach ( $oVariants as $sKey => $oVariant ) {
            $aNames = explode( trim( $this->_sMdSeparator ), $oVariant->oxarticles__oxvarselect->value );
            foreach ( $aNames as $sNameKey => $sName ) {
                $aNames[$sNameKey] = trim($sName);
            }
            $oMdVariants->addNames( $sKey,
                                    $aNames,
                                    ( $this->getConfig()->getConfigParam( 'bl_perfLoadPrice' ) ) ? $oVariant->getPrice()->getBruttoPrice() : null,
                                    $oVariant->getLink() );
        }

        return $oMdVariants;
    }

    /**
     * Generate variants from selection lists
     *
     * @param array  $aSels    ids of selection list
     * @param object $oArticle parent article
     *
     * @return null
     */
    public function genVariantFromSell( $aSels, $oArticle )
    {
        $oVariants = $oArticle->getAdminVariants();
        $myConfig  = $this->getConfig();
        $myUtils   = oxRegistry::getUtils();
        $myLang    = oxRegistry::getLang();
        $aConfLanguages = $myLang->getLanguageIds();

        foreach ($aSels as $sSelId) {
            $oSel = oxNew("oxi18n");
            $oSel->setEnableMultilang( false );
            $oSel->init( 'oxselectlist' );
            $oSel->load( $sSelId );
            $sVarNameUpdate = "";
            foreach ($aConfLanguages as $sKey => $sLang) {
                $sPrefix = $myLang->getLanguageTag($sKey);
                $aSelValues = $myUtils->assignValuesFromText($oSel->{"oxselectlist__oxvaldesc".$sPrefix}->value );
                foreach ($aSelValues as $sI => $oValue ) {
                    $aValues[$sI][$sKey] = $oValue;
                }
                $aSelTitle[$sKey] = $oSel->{"oxselectlist__oxtitle".$sPrefix}->value;
                $sMdSeparator = ($oArticle->oxarticles__oxvarname->value) ? $this->_sMdSeparator: '';
                if ( $sVarNameUpdate ) {
                    $sVarNameUpdate .= ", ";
                }
                $sVarName = oxDb::getDb()->quote($sMdSeparator.$aSelTitle[$sKey]);
                $sVarNameUpdate .= "oxvarname".$sPrefix." = CONCAT(oxvarname".$sPrefix.", ".$sVarName.")";
            }
            $oMDVariants = $this->_assignValues( $aValues, $oVariants, $oArticle, $aConfLanguages);
            if ( $myConfig->getConfigParam( 'blUseMultidimensionVariants' ) ) {
                $oAttribute = oxNew("oxattribute");
                $oAttribute->assignVarToAttribute( $oMDVariants, $aSelTitle );
            }
            $this->_updateArticleVarName( $sVarNameUpdate, $oArticle->oxarticles__oxid->value );
        }
    }

    /**
     * Assigns values of selection list to variants
     *
     * @param array  $aValues        multilang values of selection list
     * @param object $oVariants      variant list
     * @param object $oArticle       parent article
     * @param array  $aConfLanguages array of all active languages
     *
     * @return mixed
     */
    protected function _assignValues( $aValues, $oVariants, $oArticle, $aConfLanguages)
    {
        $myConfig = $this->getConfig();
        $myLang    = oxRegistry::getLang();
        $iCounter = 0;
        $aVarselect = array(); //multilanguage names of existing variants
        //iterating through all select list values (eg. $oValue->name = S, M, X, XL)
        for ( $i=0; $i<count($aValues); $i++ ) {
            $oValue = $aValues[$i][0];
            $dPriceMod = $this->_getValuePrice( $oValue, $oArticle->oxarticles__oxprice->value);
            if ( $oVariants->count() > 0 ) {
                //if we have any existing variants then copying each variant with $oValue->name
                foreach ( $oVariants as $oSimpleVariant ) {
                    if ( !$iCounter ) {
                        //we just update the first variant
                        $oVariant = oxNew("oxarticle");
                        $oVariant->setEnableMultilang(false);
                        $oVariant->load($oSimpleVariant->oxarticles__oxid->value);
                        $oVariant->oxarticles__oxprice->setValue( $oVariant->oxarticles__oxprice->value + $dPriceMod );
                        //assign for all languages
                        foreach ( $aConfLanguages as $sKey => $sLang ) {
                            $oValue = $aValues[$i][$sKey];
                            $sPrefix = $myLang->getLanguageTag($sKey);
                            $aVarselect[$oSimpleVariant->oxarticles__oxid->value][$sKey] = $oVariant->{"oxarticles__oxvarselect".$sPrefix}->value;
                            $oVariant->{'oxarticles__oxvarselect'.$sPrefix}->setValue($oVariant->{"oxarticles__oxvarselect".$sPrefix}->value.$this->_sMdSeparator.$oValue->name);
                        }
                        $oVariant->oxarticles__oxsort->setValue($oVariant->oxarticles__oxsort->value * 10);
                        $oVariant->save();
                        $sVarId = $oSimpleVariant->oxarticles__oxid->value;
                    } else {
                        //we create new variants
                        foreach ($aVarselect[$oSimpleVariant->oxarticles__oxid->value] as $sKey => $sVarselect) {
                            $oValue = $aValues[$i][$sKey];
                            $sPrefix = $myLang->getLanguageTag($sKey);
                            $aParams['oxarticles__oxvarselect'.$sPrefix] = $sVarselect.$this->_sMdSeparator.$oValue->name;
                        }
                        $aParams['oxarticles__oxartnum'] = $oSimpleVariant->oxarticles__oxartnum->value . "-" . $iCounter;
                        $aParams['oxarticles__oxprice'] = $oSimpleVariant->oxarticles__oxprice->value + $dPriceMod;
                        $aParams['oxarticles__oxsort'] = $oSimpleVariant->oxarticles__oxsort->value*10 + 10*$iCounter;
                        $aParams['oxarticles__oxstock'] = 0;
                        $aParams['oxarticles__oxstockflag'] = $oSimpleVariant->oxarticles__oxstockflag->value;
                        $aParams['oxarticles__oxisconfigurable'] = $oSimpleVariant->oxarticles__oxisconfigurable->value;
                        $sVarId = $this->_createNewVariant( $aParams, $oArticle->oxarticles__oxid->value );
                        if ( $myConfig->getConfigParam( 'blUseMultidimensionVariants' ) ) {
                            $oAttrList = oxNew('oxattribute');
                            $aIds = $oAttrList->getAttributeAssigns( $oSimpleVariant->oxarticles__oxid->value);
                            $aMDVariants["mdvar_".$sVarId] = $aIds;
                        }
                    }
                    if ( $myConfig->getConfigParam( 'blUseMultidimensionVariants' ) ) {
                        $aMDVariants[$sVarId] = $aValues[$i];
                    }
                }
                $iCounter++;
            } else {
                //in case we don't have any variants then we just create variant(s) with $oValue->name
                $iCounter++;
                foreach ($aConfLanguages as $sKey => $sLang) {
                    $oValue = $aValues[$i][$sKey];
                    $sPrefix = $myLang->getLanguageTag($sKey);
                    $aParams['oxarticles__oxvarselect'.$sPrefix] = $oValue->name;
                }
                $aParams['oxarticles__oxartnum'] = $oArticle->oxarticles__oxartnum->value . "-" . $iCounter ;
                $aParams['oxarticles__oxprice'] = $oArticle->oxarticles__oxprice->value + $dPriceMod;
                $aParams['oxarticles__oxsort'] = $iCounter * 100; // reduction
                $aParams['oxarticles__oxstock'] = 0;
                $aParams['oxarticles__oxstockflag'] = $oArticle->oxarticles__oxstockflag->value;
                $aParams['oxarticles__oxisconfigurable'] = $oArticle->oxarticles__oxisconfigurable->value;
                $sVarId = $this->_createNewVariant( $aParams, $oArticle->oxarticles__oxid->value );
                if ( $myConfig->getConfigParam( 'blUseMultidimensionVariants' ) ) {
                    $aMDVariants[$sVarId] = $aValues[$i];
                }
            }
        }
        return $aMDVariants;
    }

    /**
     * Returns article price
     *
     * @param object $oValue       selection list value
     * @param double $dParentPrice parent article price
     *
     * @return double
     */
    protected function _getValuePrice( $oValue, $dParentPrice)
    {
        $myConfig = $this->getConfig();
        $dPriceMod = 0;
        if ( $myConfig->getConfigParam( 'bl_perfLoadSelectLists' ) && $myConfig->getConfigParam( 'bl_perfUseSelectlistPrice' ) ) {
            if ($oValue->priceUnit == 'abs') {
                $dPriceMod = $oValue->price;
            } elseif ($oValue->priceUnit == '%') {
                $dPriceModPerc = abs($oValue->price)*$dParentPrice/100.0;
                if (($oValue->price) >= 0.0) {
                    $dPriceMod = $dPriceModPerc;
                } else {
                    $dPriceMod = -$dPriceModPerc;
                }
            }
        }
        return $dPriceMod;
    }

    /**
     * Creates new article variant.
     *
     * @param array  $aParams   assigned parameters
     * @param string $sParentId parent article id
     *
     * @return null
     */
    protected function _createNewVariant( $aParams = null, $sParentId = null)
    {
        // checkbox handling
        $aParams['oxarticles__oxactive'] = 0;

            // shopid
            $sShopID = oxSession::getVar( "actshop");
            $aParams['oxarticles__oxshopid'] = $sShopID;

        // varianthandling
        $aParams['oxarticles__oxparentid'] = $sParentId;

        $oArticle = oxNew("oxi18n");
        $oArticle->setEnableMultilang(false);
        $oArticle->init( 'oxarticles' );
        $oArticle->assign( $aParams);

            //echo $aParams['oxarticles__oxartnum']."---";
            $oArticle->save();

        return $oArticle->getId();
    }

    /**
     * Inserts article variant name for all languages
     *
     * @param string $sUpdate query for update variant name
     * @param string $sArtId  parent article id
     *
     * @return null
     */
    protected function _updateArticleVarName( $sUpdate, $sArtId )
    {
        $oDb = oxDb::getDb();
        $sUpdate = "update oxarticles set " . $sUpdate . " where oxid = " . $oDb->quote( $sArtId );
        $oDb->Execute( $sUpdate );
    }

    /**
     * Check if variant is multidimensional
     *
     * @param oxArticle $oArticle Article object
     *
     * @return bool
     */
    public function isMdVariant( $oArticle )
    {
        if ( $this->getConfig()->getConfigParam( 'blUseMultidimensionVariants' ) ) {
            if ( strpos( $oArticle->oxarticles__oxvarselect->value, trim($this->_sMdSeparator) ) !== false ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Creates array/matrix with variant selections
     *
     * @param oxArticleList $oVariantList  variant list
     * @param int           $iVarSelCnt    possible variant selection count
     * @param array         &$aFilter      active filter array
     * @param string        $sActVariantId active variant id
     *
     * @return array
     */
    protected function _fillVariantSelections( $oVariantList, $iVarSelCnt, &$aFilter, $sActVariantId )
    {
        $aSelections = array();

        // filling selections
        foreach ( $oVariantList as $oVariant ) {

            $aNames = $this->_getSelections( $oVariant->oxarticles__oxvarselect->getRawValue() );
            $blActive = ( $sActVariantId === $oVariant->getId() ) ? true : false;
            for ( $i = 0; $i < $iVarSelCnt; $i++ ) {
                $sName = isset( $aNames[$i] ) ? trim($aNames[$i]) : false;
                if ($sName) {
                    $sHash = md5($sName );

                    // filling up filter
                    if ( $blActive ) {
                        $aFilter[$i] = $sHash;
                    }

                    $aSelections[$oVariant->getId()][$i] = array( 'name' => $sName, 'disabled' => null, 'active' => false, 'hash' => $sHash );
                }
            }
        }
        return $aSelections;
    }

    /**
     * Cleans up user given filter. If filter was empty - returns false
     *
     * @param array $aFilter user given filter
     *
     * @return array | bool
     */
    protected function _cleanFilter( $aFilter )
    {
        $aCleanFilter = false;
        if ( is_array( $aFilter ) && count( $aFilter ) ) {
            foreach ( $aFilter as $iKey => $sFilter ) {
                if ( $sFilter ) {
                    $aCleanFilter[$iKey] = $sFilter;
                }
            }
        }

        return $aCleanFilter;
    }

    /**
     * Applies filter on variant selection array
     *
     * @param array $aSelections selections
     * @param array $aFilter     filter
     *
     * @return array
     */
    protected function _applyVariantSelectionsFilter( $aSelections, $aFilter )
    {
        $iMaxActiveCount = 0;
        $sMostSuitableVariantId = null;
        $blPerfectFit = false;
        // applying filters, disabling/activating items
        if ( ( $aFilter = $this->_cleanFilter( $aFilter ) ) ) {
            $aFilterKeys = array_keys( $aFilter );
            $iFilterKeysCount = count( $aFilter );
            foreach ( $aSelections as $sVariantId => &$aLineSelections ) {
                $iActive = 0;
                foreach ( $aFilter as $iKey => $sVal ) {
                    if ( strcmp( $aLineSelections[$iKey]['hash'], $sVal ) === 0 ) {
                        $aLineSelections[$iKey]['active'] = true;
                        $iActive++;
                    } else {
                        foreach ($aLineSelections as $iOtherKey => &$aLineOtherVariant) {
                            if ( $iKey != $iOtherKey ) {
                                $aLineOtherVariant['disabled'] = true;
                            }
                        }
                    }
                }
                foreach ($aLineSelections as $iOtherKey => &$aLineOtherVariant) {
                    if ( !in_array( $iOtherKey, $aFilterKeys ) ) {
                        $aLineOtherVariant['disabled'] = !($iFilterKeysCount == $iActive);
                    }
                }

                $blFitsAll = $iActive && (count($aLineSelections) == $iActive) && ($iFilterKeysCount == $iActive);
                if (($iActive > $iMaxActiveCount) || (!$blPerfectFit && $blFitsAll)) {
                    $blPerfectFit = $blFitsAll;
                    $sMostSuitableVariantId = $sVariantId;
                    $iMaxActiveCount = $iActive;
                }

                unset( $aLineSelections );
            }
        }
        return array($aSelections, $sMostSuitableVariantId, $blPerfectFit);
    }

    /**
     * Builds variant selections list - array containing oxVariantSelectList
     *
     * @param array $aVarSelects variant selection titles
     * @param array $aSelections variant selections
     *
     * @return array
     */
    protected function _buildVariantSelectionsList( $aVarSelects, $aSelections )
    {
        // creating selection lists
        foreach ( $aVarSelects as $iKey => $sLabel ) {
            $aVariantSelections[$iKey] = oxNew( "oxVariantSelectList", $sLabel, $iKey );
        }

        // building variant selections
        foreach ( $aSelections as $aLineSelections ) {
            foreach ( $aLineSelections as $oPos => $aLine ) {
                $aVariantSelections[$oPos]->addVariant( $aLine['name'], $aLine['hash'], $aLine['disabled'], $aLine['active'] );
            }
        }

        return $aVariantSelections;
    }

    /**
     * In case multidimentional variants ON explodes title by _sMdSeparator
     * and returns array, else - returns array containing title
     *
     * @param string $sTitle title to process
     *
     * @return array
     */
    protected function _getSelections( $sTitle )
    {

        if ( $this->getConfig()->getConfigParam( 'blUseMultidimensionVariants' ) ) {
            $aSelections = explode( $this->_sMdSeparator, $sTitle );
        } else {
            $aSelections = array( $sTitle );
        }

        return $aSelections;
    }

    /**
     * Builds variant selection list
     *
     * @param string        $sVarName      product (parent product) oxvarname value
     * @param oxarticlelist $oVariantList  variant list
     * @param array         $aFilter       variant filter
     * @param string        $sActVariantId active variant id
     * @param int           $iLimit        limit variant lists count (if non zero, return limited number of multidimensional variant selections)
     *
     * @return Ambigous false | array
     */
    public function buildVariantSelections( $sVarName, $oVariantList, $aFilter, $sActVariantId, $iLimit = 0 )
    {
        $aReturn = false;


        // assigning variants
        $aVarSelects = $this->_getSelections( $sVarName );

        if ($iLimit) {
            $aVarSelects = array_slice($aVarSelects, 0, $iLimit);
        }
        if ( ( $iVarSelCnt = count( $aVarSelects ) ) ) {

            // filling selections
            $aRawVariantSelections = $this->_fillVariantSelections( $oVariantList, $iVarSelCnt, $aFilter, $sActVariantId );

            // applying filters, disabling/activating items
            list($aRawVariantSelections, $sActVariantId, $blPerfectFit) = $this->_applyVariantSelectionsFilter( $aRawVariantSelections, $aFilter );
            // creating selection lists
            $aVariantSelections = $this->_buildVariantSelectionsList( $aVarSelects, $aRawVariantSelections );

            $oCurrentVariant = null;
            if ($sActVariantId) {
                $oCurrentVariant = $oVariantList[$sActVariantId];
            }



            return array(
                'selections' => $aVariantSelections,
                'rawselections' => $aRawVariantSelections,
                'oActiveVariant' => $oCurrentVariant,
                'blPerfectFit' => $blPerfectFit
            );
        }
        return false;
    }
}
