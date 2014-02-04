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
 * lexware manager
 *
 * @package model
 */
class oxImex extends oxBase
{
    /**
     * Performs Lexware export to file.
     *
     * @param integer $iStart    Start writing to file from line
     * @param integer $iLines    Amount of lines to write
     * @param string  $sFilepath Path to export file
     *
     * @return bool
     */
    public function exportLexwareArticles( $iStart, $iLines, $sFilepath)
    {
        $myConfig = $this->getConfig();
        $oDb      = oxDb::getDb();

        $sArticleTable = getViewName('oxarticles');

        $sSelect = "select count(oxid) from $sArticleTable ";
        $iSize = (int) $oDb->getOne( $sSelect );

        if ( $iStart < $iSize) {
            $fp = fopen( $sFilepath, "ab");
            if ( !$iStart) {
                // first time, write header
                fwrite( $fp, "\"Artikelnummer\";\"Bezeichnung\";\"Einheit\";\"Gewicht\";\"Matchcode\";\"Preis pro Anzahl\";\"Warengruppe\";\"Warengr.-Kurzbez.\";\"Warengr.-Steuersatz\";\"Warengr.-Konto Inland\";\"Warengr.-Konto Ausland\";\"Warengr.-Konto EG\";\"Preis 1\";\"Preis 2\";\"Preis 3\";\"Preis I/1\";\"Preis I/2\";\"Preis I/3\";\"Preis II/1\";\"Preis II/2\";\"Preis II/3\";\"Preis III/1\";\"Preis III/2\";\"Preis III/3\";\"B/N\";\"Lagerartikel\";\"EK 1\";\"Währung EK1\";\"EK 2\";\"Währung EK2\";\"Staffelmenge 1\";\"Staffelmenge 2\";\"Staffelmenge 3\";\"Lieferantennummer 1\";\"Lieferantennummer 2\";\"Bestellmenge Lf.1\";\"Bestellmenge Lf.2\";\"Bestellnr. Lf.1\";\"Bestellnr. Lf.2\";\"Lieferzeit Lf.1\";\"Lieferzeit Lf.2\";\"Lagerbestand\";\"Mindestbestand\";\"Lagerort\";\"Bestellte Menge\";\"Stückliste\";\"Internet\";\"Text\"\r\n");
            }
            $oldMode = $oDb->setFetchMode( oxDb::FETCH_MODE_ASSOC );
            $sSelect = "select * from $sArticleTable ";
            $rs = $oDb->selectLimit( $sSelect, $iLines, $iStart);
            $oDb->setFetchMode( $oldMode);

            while (!$rs->EOF) {
                $oArticle = oxNew( "oxarticle" );
                $blAdmin = $this->isAdmin();
                // TODO: this place could be optimized. please check what we can do.
                $this->setAdminMode( false );
                $oArticle->load( $rs->fields['OXID']);
                $this->setAdminMode( $blAdmin );

                $sSelect = "select oxtitle from ".$oArticle->getViewName()." where oxid = " . $oDb->quote( $oArticle->oxarticles__oxparentid->value );
                $oTitle = $oDb->getOne( $sSelect );
                if ($oTitle != false && strlen ($oTitle)) {
                    $nTitle = $this->interForm($oTitle);
                } else {
                    $nTitle = $this->interForm($oArticle->oxarticles__oxtitle->value);
                }


                $sToFile = $oArticle->oxarticles__oxartnum->value            // Artikelnummer
                //.";".$this->interForm($oArticle->oxarticles__oxshortdesc->value." ".$oArticle->oxarticles__oxvarselect->value) // Bezeichnung
                .";".$nTitle." ".$this->interForm($oArticle->oxarticles__oxvarselect->value) // Bezeichnung
                .";"."Stueck"                        // Einheit
                .";".$oArticle->oxarticles__oxweight->value                  // Gewicht
                .";".$oArticle->oxarticles__oxartnum->value                  // Matchcode
                .";"."1,000"                         // Preis pro Anzahl
                .";"                                  // Warengruppe
                .";"                                  // Warengr.-Kurzbez.
                .";"                                 // Warengr.-Steuersatz
                .";"                                  // Warengr.-Konto Inland
                .";"                                  // Warengr.-Konto Ausland
                .";"                                  // Warengr.-Konto EG
                .";".number_format($oArticle->oxarticles__oxprice->value, 2, '.', '')  // Preis 1
                .";"                                  // Preis 2
                .";"                                 // Preis 3
                .";"                                 // Preis I/1
                .";"                                 // Preis I/2
                .";"                                  // Preis I/3
                .";"                                  // Preis II/1
                .";"                                  // Preis II/2
                .";"                                  // Preis II/3
                .";"                                  // Preis III/1
                .";"                                  // Preis III/2
                .";"                                  // Preis III/3
                .";"                           // B/N
                .";"                           // Lagerartikel
                //.";".number_format($oArticle->oxarticles__oxtprice->value, 2, '.', '')// EK 1
                // #343 fix
                .";".number_format($oArticle->oxarticles__oxbprice->value, 2, '.', '')// EK 1
                .";"                           // Währung EK1
                .";"                           // EK 2
                .";"                           // Währung EK2
                .";"                           // Staffelmenge 1
                .";"                           // Staffelmenge 2
                .";"                           // Staffelmenge 3
                .";"                           // Lieferantennummer 1
                .";"                           // Lieferantennummer 2
                .";"                           // Bestellmenge Lf.1
                .";"                           // Bestellmenge Lf.2
                .";"                           // Bestellnr. Lf.1
                .";"                           // Bestellnr. Lf.2
                .";"                           // Lieferzeit Lf.1
                .";"                           // Lieferzeit Lf.2
                .";".$oArticle->oxarticles__oxstock->value           // Lagerbestand
                .";"                           // Mindestbestand
                .";"                           // Lagerort
                .";"                           // Bestellte Menge
                .";"                           // Stückliste
                .";1"                              // Internet
                .";".$this->interForm( $oArticle->oxarticles__oxshortdesc->value.$oArticle->getLongDesc())// Text
                .";";
                $sToFile .= "\r\n";

                fwrite( $fp, $sToFile);
                $rs->moveNext();
            }

            fclose( $fp );
            return true;
        }

        return false;

    }

    /**
     * Ensures, that the given data can be put in the csv
     *
     * @param string $nValue given string
     *
     * @return string
     */
    function interFormSimple( $nValue )
    {
        $nValue = str_replace( "\r", "", $nValue );
        $nValue = str_replace( "\n", " ", $nValue );
        $nValue = str_replace( '"', '""', $nValue );
        return $nValue;
    }

    /**
     * Replaces some special chars to HTML compatible codes, returns string
     * with replaced chars.
     *
     * @param string $nValue string to replace special chars
     * @param object $oObj   object
     *
     * @return string
     */
    function interForm( $nValue, $oObj = null)
    {   // thnx to Volker Dörk for this function and his help here

        // #387A skipping conversion for fields where info must be passed in original format
        $aFieldTypesToSkip = array("text", "oxshortdesc", "oxlongdesc");
        $blSkipStrpTags = false;
        if ( $oObj != null) {
            // using object field "fldtype", to skip processing because usually
            // this type of field is used for HTML text
            //
            // you may change field to "fldname" and add to $aFieldTypesToSkip
            // "oxlongdesc" value to skip only longdesc field
            //
            if ( in_array( $oObj->fldtype, $aFieldTypesToSkip ) || in_array( $oObj->fldname, $aFieldTypesToSkip ) ) {
                $blSkipStripTags = true;
            }
        }

        //removing simple & (and not  &uuml; chars)
        //(not full just a simple check for existing customers for cases like Johnson&Johnson)

        $oStr = getStr();
        if ( $oStr->strpos( $nValue, "&" ) !== false && $oStr->strpos($nValue, ";" ) == false ) {
            $nValue = str_replace("&", "&amp;", $nValue);
        }

        $nValue = str_replace( "&nbsp;", " ", $nValue);
        $nValue = str_replace( "&auml;", "ä", $nValue);
        $nValue = str_replace( "&ouml;", "ö", $nValue);
        $nValue = str_replace( "&uuml;", "ü", $nValue);
        $nValue = str_replace( "&Auml;", "Ä", $nValue);
        $nValue = str_replace( "&Ouml;", "Ö", $nValue);
        $nValue = str_replace( "&Uuml;", "Ü", $nValue);
        $nValue = str_replace( "&szlig;", "ß", $nValue);

        // usually & symbol goes (or should go) like that:
        // "& text...", so we predict that this is a rule
        // and replace it with special HTML code
        $nValue = str_replace( "& ", "&amp; ", $nValue);

        $nValue = str_replace( "\"", "'", $nValue);
        $nValue = str_replace( "(", "'", $nValue);
        $nValue = str_replace( ")", "'", $nValue);
        $nValue = str_replace( "\r\n", "", $nValue);
        $nValue = str_replace( "\n", "", $nValue);

        if ( !$blSkipStripTags) {
            $nValue = strip_tags( $nValue );
        }

        return $nValue;
    }

    /**
     * Returns formatted price (grouped thousands, etc.).
     *
     * @param float $nPrice Price to format
     *
     * @return string
     */
    function internPrice( $nPrice)
    {  // thnx to Volker Dörk for this function and his help here
        $nPrice = $this->interForm($nPrice);
        $nPrice = number_format( (double)$nPrice, 2, '.', '');
        return $nPrice;
    }

    /**
     * Returns XML compatible text for LexwareOrders export.
     *
     * @param integer $iFromOrderNr Order from (default null)
     * @param integer $iToOrderNr   Order number
     *
     * @return string
     */
    function exportLexwareOrders( $iFromOrderNr = "", $iToOrderNr = "")
    {
        // thnx to Volker Dörk for this function and his help here
        $myConfig = $this->getConfig();

        $sNewLine = "\r\n";

        $sSelect = "select * from oxorder where 1 ";

        if ( $iFromOrderNr !== "" ) {
            $iFromOrderNr = (int)$iFromOrderNr;
            $sSelect .= "and oxordernr >= $iFromOrderNr ";
        }

        if ( $iToOrderNr !== "" ) {
            $iToOrderNr = (int)$iToOrderNr;
            $sSelect .= "and oxordernr <= $iToOrderNr ";
        }

        $oOrderlist = oxNew( "oxlist" );
        $oOrderlist->init( "oxorder" );
        $oOrderlist->selectString( $sSelect );

        if ( !$oOrderlist->count() ) {
            return null;
        }

        $sCharset = $this->_getCharset();

        $sExport  = "<?xml version=\"1.0\" encoding=\"{$sCharset}\"?>$sNewLine";
        $sExport .= "<Bestellliste>$sNewLine";
        $sRet     = $sExport;

        foreach ( $oOrderlist as $oOrder ) {
            // Convert each amount of money with currency rate of the order
            $dOrderCurRate = (double)$oOrder->oxorder__oxcurrate->value;

            $oUser = oxNew( "oxuser" );
            $oUser->load( $oOrder->oxorder__oxuserid->value );

            $sExport  = "<Bestellung " . $this->_convertStr( "zurückgestellt" ) . "=\"Nein\" bearbeitet=\"Nein\" " . $this->_convertStr( "übertragen" ) . "=\"Nein\">$sNewLine";
            $sExport .= "<Bestellnummer>".$oOrder->oxorder__oxordernr->value."</Bestellnummer>$sNewLine";
            $sExport .= "<Rechnungsnummer>".$oOrder->oxorder__oxbillnr->value."</Rechnungsnummer>$sNewLine";
            $sExport .= "<Standardwaehrung>978</Standardwaehrung>$sNewLine";
            $sExport .= "<Bestelldatum>$sNewLine";
            $sDBDate = oxRegistry::get("oxUtilsDate")->formatDBDate($oOrder->oxorder__oxorderdate->value);
            $sExport .= "<Datum>".substr($sDBDate, 0, 10)."</Datum>$sNewLine";
            $sExport .= "<Zeit>".substr($sDBDate, 11, 8)."</Zeit>$sNewLine";
            $sExport .= "</Bestelldatum>$sNewLine";
            $sExport .= "<Kunde>$sNewLine";

            $sExport .= "<Kundennummer>"./*$this->interForm($oUser->oxuser__oxcustnr->value).*/"</Kundennummer>$sNewLine";
            $sExport .= "<Firmenname>".$this->interForm($oOrder->oxorder__oxbillcompany->value)."</Firmenname>$sNewLine";
            $sExport .= "<Anrede>".$this->interForm(oxRegistry::getLang()->translateString($oOrder->oxorder__oxbillsal->value))."</Anrede>$sNewLine";
            $sExport .= "<Vorname>".$this->interForm($oOrder->oxorder__oxbillfname->value)."</Vorname>$sNewLine";
            $sExport .= "<Name>".$this->interForm($oOrder->oxorder__oxbilllname->value)."</Name>$sNewLine";
            $sExport .= "<Strasse>".$this->interForm($oOrder->oxorder__oxbillstreet->value)." ".$this->interForm($oOrder->oxorder__oxbillstreetnr->value)."</Strasse>$sNewLine";
            $sExport .= "<PLZ>".$this->interForm($oOrder->oxorder__oxbillzip->value)."</PLZ>$sNewLine";
            $sExport .= "<Ort>".$this->interForm($oOrder->oxorder__oxbillcity->value)."</Ort>$sNewLine";
            $sExport .= "<Bundesland>".""."</Bundesland>$sNewLine";
            $sExport .= "<Land>".$this->interForm($oOrder->oxorder__oxbillcountry->value)."</Land>$sNewLine";
            $sExport .= "<Email>".$this->interForm($oOrder->oxorder__oxbillemail->value)."</Email>$sNewLine";
            $sExport .= "<Telefon>".$this->interForm($oOrder->oxorder__oxbillfon->value)."</Telefon>$sNewLine";
            $sExport .= "<Telefon2>".$this->interForm($oUser->oxuser__oxprivfon->value)."</Telefon2>$sNewLine";
            $sExport .= "<Fax>".$this->interForm($oOrder->oxorder__oxbillfax->value)."</Fax>$sNewLine";

            $sDelComp    = "";
            $sDelfName   = "";
            $sDellName   = "";
            $sDelStreet  = "";
            $sDelZip     = "";
            $sDelCity    = "";
            $sDelCountry = "";

            // lieferadresse
            if ( $oOrder->oxorder__oxdellname->value) {
                $sDelComp   = $oOrder->oxorder__oxdelcompany->value;
                $sDelfName  = $oOrder->oxorder__oxdelfname->value;
                $sDellName  = $oOrder->oxorder__oxdellname->value;
                $sDelStreet = $oOrder->oxorder__oxdelstreet->value." ".$oOrder->oxorder__oxdelstreetnr->value;
                $sDelZip    = $oOrder->oxorder__oxdelzip->value;
                $sDelCity   = $oOrder->oxorder__oxdelcity->value;
                $sDelCountry= $oOrder->oxorder__oxdelcountry->value;
            }

            $sExport .= "<Lieferadresse>$sNewLine";
            $sExport .= "<Firmenname>".$this->interForm($sDelComp)."</Firmenname>$sNewLine";
            $sExport .= "<Vorname>".$this->interForm($sDelfName)."</Vorname>$sNewLine";
            $sExport .= "<Name>".$this->interForm($sDellName)."</Name>$sNewLine";
            $sExport .= "<Strasse>".$this->interForm($sDelStreet)."</Strasse>$sNewLine";
            $sExport .= "<PLZ>".$this->interForm($sDelZip)."</PLZ>$sNewLine";
            $sExport .= "<Ort>".$this->interForm($sDelCity)."</Ort>$sNewLine";
            $sExport .= "<Bundesland>".""."</Bundesland>$sNewLine";
            $sExport .= "<Land>".$this->interForm($sDelCountry)."</Land>$sNewLine";
            $sExport .= "</Lieferadresse>$sNewLine";
            $sExport .= "<Matchcode>".$this->interForm($oOrder->oxorder__oxbilllname->value).", ".$this->interForm($oOrder->oxorder__oxbillfname->value)."</Matchcode>$sNewLine";

            // ermitteln ob steuerbar oder nicht
            $sCountry = strtolower( $oUser->oxuser__oxcountryid->value );
            $aHomeCountry = $myConfig->getConfigParam( 'aHomeCountry' );
            $sSteuerbar = ( is_array( $aHomeCountry ) && in_array( $sCountry, $aHomeCountry ) ) ? "ja" : "nein";

            $sExport .= "<fSteuerbar>".$this->interForm( $sSteuerbar )."</fSteuerbar>$sNewLine";
            $sExport .= "</Kunde>$sNewLine";
            $sExport .= "<Artikelliste>$sNewLine";
            $sRet .= $sExport;

            $dSumNetPrice = 0;
            $dSumBrutPrice = 0;

            $oOrderArticles = $oOrder->getOrderArticles( true );
            foreach ($oOrderArticles as $oOrderArt) {

                $dVATSet = array_search( $oOrderArt->oxorderarticles__oxvat->value, $myConfig->getConfigParam( 'aLexwareVAT' ) );
                $sExport  = "   <Artikel>$sNewLine";
                //$sExport .= "   <Artikelzusatzinfo><Nettostaffelpreis>".$this->InternPrice( $oOrderArt->oxorderarticles__oxnetprice->value)."</Nettostaffelpreis></Artikelzusatzinfo>$sNewLine";
                $sExport .= "   <Artikelzusatzinfo><Nettostaffelpreis></Nettostaffelpreis></Artikelzusatzinfo>$sNewLine";
                $sExport .= "   <SteuersatzID>".$dVATSet."</SteuersatzID>$sNewLine";
                $sExport .= "   <Steuersatz>".$this->internPrice($oOrderArt->oxorderarticles__oxvat->value/100)."</Steuersatz>$sNewLine";
                $sExport .= "   <Artikelnummer>".$oOrderArt->oxorderarticles__oxartnum->value."</Artikelnummer>$sNewLine";
                $sExport .= "   <Anzahl>".$oOrderArt->oxorderarticles__oxamount->value."</Anzahl>$sNewLine";
                $sExport .= "   <Produktname>".$this->interForm( $oOrderArt->oxorderarticles__oxtitle->value);
                if ( $oOrderArt->oxorderarticles__oxselvariant->value) {
                    $sExport .= "/".$oOrderArt->oxorderarticles__oxselvariant->value;
                }
                $sExport .= "   </Produktname>$sNewLine";
                $sExport .= "   <Rabatt>0.00</Rabatt>$sNewLine";

                $dUnitPrice = $oOrderArt->oxorderarticles__oxbrutprice->value/$oOrderArt->oxorderarticles__oxamount->value;
                if ($dOrderCurRate > 0) {
                    $dUnitPrice /= $dOrderCurRate;
                }
                $sExport .= "   <Preis>".$this->internPrice($dUnitPrice)."</Preis>$sNewLine";
                $sExport .= "   </Artikel>$sNewLine";
                $sRet .= $sExport;

                $dSumNetPrice   += $oOrderArt->oxorderarticles__oxnetprice->value;
                $dSumBrutPrice  += $oOrderArt->oxorderarticles__oxbrutprice->value;
            }

            $dDiscount = $oOrder->oxorder__oxvoucherdiscount->value + $oOrder->oxorder__oxdiscount->value;
            $dDelCost = $oOrder->oxorder__oxdelcost->value;
            $dPaymentCost = $oOrder->oxorder__oxpaycost->value;
            if ($dOrderCurRate > 0) {
                $dDiscount /= $dOrderCurRate;
                $dSumNetPrice /= $dOrderCurRate;
                $dDelCost /= $dOrderCurRate;
                $dSumBrutPrice /= $dOrderCurRate;
                $dPaymentCost /= $dOrderCurRate;
            }
            $sExport  = "<GesamtRabatt>".$this->internPrice($dDiscount)."</GesamtRabatt>$sNewLine";
            $sExport .= "<GesamtNetto>".$this->internPrice($dSumNetPrice)."</GesamtNetto>$sNewLine";
            $sExport .= "<Lieferkosten>".$this->internPrice($dDelCost)."</Lieferkosten>$sNewLine";
            $sExport .= "<Zahlungsartkosten>".$this->internPrice($dPaymentCost)."</Zahlungsartkosten>$sNewLine";
            $sExport .= "<GesamtBrutto>".$this->internPrice($dSumBrutPrice)."</GesamtBrutto>$sNewLine";
            $sExport .= "<Bemerkung>".strip_tags( $oOrder->oxorder__oxremark->value)."</Bemerkung>$sNewLine";
            $sRet .= $sExport;

            $sExport  = "</Artikelliste>$sNewLine";

            $sExport .= "<Zahlung>$sNewLine";
            $oPayment = oxNew( "oxpayment" );
            $oPayment->load( $oOrder->oxorder__oxpaymenttype->value);

            $sExport .= "<Art>".$oPayment->oxpayments__oxdesc->value."</Art>$sNewLine";
            $sExport .= "</Zahlung>$sNewLine";

            $sExport .= "</Bestellung>$sNewLine";
            $sRet .= $sExport;

            $oOrder->oxorder__oxexport->setValue(1);
            $oOrder->save();
        }
        $sExport = "</Bestellliste>$sNewLine";
        $sRet .= $sExport;

        return $sRet;
    }

    /**
     * Get current charset
     *
     * @return string
     */
    protected function _getCharset()
    {
        return oxRegistry::getLang()->translateString( 'charset' );
    }

    /**
     * Converts string from 'ISO-8859-15' to defined charset
     *
     * @param string $sStr string to convert
     *
     * @return string
     */
    protected function _convertStr( $sStr )
    {
        $sCharset = $this->_getCharset();

        if ( $sCharset == 'ISO-8859-15' ) {
            return $sStr;
        }

        return $sStr = iconv( 'ISO-8859-15', $sCharset, $sStr );
    }
}
