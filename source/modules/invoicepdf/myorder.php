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
 * @package   modules
 * @copyright (C) OXID eSales AG 2003-2014
 * @version OXID eShop CE
 */

/**
 * PDF renderer helper class, used to store data, sizes etc..
 */
class PdfBlock
{
    /**
     * array of data to render
     * @var array
     */
    protected $_aCache = array();

    /**
     * string default Font
     * @var array
     */
    protected $_sFont = 'Arial';

    /**
     * Stores cacheable parameters
     *
     * @param string $sFunc   cacheable function name
     * @param array  $aParams cacheable parameters
     *
     * @return null
     */
    protected function _toCache( $sFunc, $aParams )
    {
        $oItem = new stdClass();
        $oItem->sFunc   = $sFunc;
        $oItem->aParams = $aParams;
        $this->_aCache[]  = $oItem;
    }

    /**
     * Runs and evaluates cached code
     *
     * @param object $oPdf object which methods will be executed
     *
     * @return null
     */
    public function run( $oPdf )
    {
        foreach ( $this->_aCache as $oItem ) {
            $sFn = $oItem->sFunc;
            switch ( count( $oItem->aParams ) ) {
                case 0:
                    $oPdf->$sFn();
                    break;
                case 1:
                    $oPdf->$sFn( $oItem->aParams[0]);
                    break;
                case 2:
                    $oPdf->$sFn( $oItem->aParams[0], $oItem->aParams[1] );
                    break;
                case 3:
                    $oPdf->$sFn( $oItem->aParams[0], $oItem->aParams[1], $oItem->aParams[2]);
                    break;
                case 4:
                    $oPdf->$sFn( $oItem->aParams[0], $oItem->aParams[1], $oItem->aParams[2], $oItem->aParams[3]);
                    break;
            }
        }
    }

    /**
     * Caches Line call with parameters
     *
     * @param int $iLPos    left position
     * @param int $iLHeight left height
     * @param int $iRPos    right position
     * @param int $iRHeight right height
     *
     * @return null
     */
    public function line( $iLPos, $iLHeight, $iRPos, $iRHeight )
    {
        $this->_toCache( 'Line', array( $iLPos, $iLHeight, $iRPos, $iRHeight ) );
    }

    /**
     * Caches Text call with parameters
     *
     * @param int    $iLPos    left position
     * @param int    $iLHeight height
     * @param string $sString  string to write
     *
     * @return null
     */
    public function text( $iLPos, $iLHeight, $sString )
    {
        $this->_toCache( 'Text', array( $iLPos, $iLHeight, $sString ) );
    }

    /**
     * Caches SetFont call with parameters
     *
     * @param string $sType   font type (Arial, Tahoma ...)
     * @param string $sWeight font weight ('', 'B', 'U' ...)
     * @param string $sSize   font size ('10', '8', '6' ...)
     *
     * @return null
     */
    public function font( $sType, $sWeight, $sSize )
    {
        $this->_toCache( 'SetFont', array( $sType, $sWeight, $sSize ) );
    }

    /**
     * Adjusts height after new page addition
     *
     * @param int $iDelta new height
     *
     * @return null
     */
    public function ajustHeight( $iDelta )
    {
        foreach ( $this->_aCache as $key => $oItem ) {
            switch ( $oItem->sFunc ) {
                case 'Line':
                    $this->_aCache[$key]->aParams[3] += $iDelta;
                    $this->_aCache[$key]->aParams[1] += $iDelta;
                    break;
                case 'Text':
                    $this->_aCache[$key]->aParams[1] += $iDelta;
                    break;
            }
        }
    }

    /**
     * Caches SetFont call with parameters
     *
     * @return string
     */
    public function getFont()
    {
        return $this->_sFont;
    }
}

/**
 * Order summary class
 */
class PdfArticleSummary extends PdfBlock
{

    /**
     * order object
     * @var object
     */
    protected $_oData = null;

    /**
     * pdf object
     * @var object
     */
    protected $_oPdf = null;

    /**
     * Constructor
     *
     * @param object $oData order object
     * @param object $oPdf  pdf object
     *
     * @return null
     */
    public function __construct( $oData, $oPdf )
    {
        $this->_oData = $oData;
        $this->_oPdf = $oPdf;
    }

    /**
     * Sets total costs values using order without discount
     *
     * @param int &$iStartPos text start position
     *
     * @return none
     */
    protected function _setTotalCostsWithoutDiscount( &$iStartPos )
    {
        $oLang = oxLang::getInstance();

        // products netto price
        $this->line( 15, $iStartPos + 1, 195, $iStartPos + 1 );
        $sNetSum = $oLang->formatCurrency( $this->_oData->oxorder__oxtotalnetsum->value, $this->_oData->getCurrency() ).' '.$this->_oData->getCurrency()->name;
        $this->text( 45, $iStartPos + 4, $this->_oData->translate( 'ORDER_OVERVIEW_PDF_ALLPRICENETTO' ) );
        $this->text( 195 - $this->_oPdf->getStringWidth( $sNetSum ), $iStartPos + 4, $sNetSum );

        // #345 - product VAT info
        $iCtr = 0;
        foreach ( $this->_oData->getVats() as $iVat => $dVatPrice ) {
            $iStartPos += 4 * $iCtr;
            $sVATSum = $oLang->formatCurrency( $dVatPrice, $this->_oData->getCurrency() ).' '.$this->_oData->getCurrency()->name;
            $this->text( 45, $iStartPos + 8, $this->_oData->translate( 'ORDER_OVERVIEW_PDF_ZZGLVAT' ).$iVat.$this->_oData->translate( 'ORDER_OVERVIEW_PDF_PERCENTSUM' ) );
            $this->text( 195 - $this->_oPdf->getStringWidth( $sVATSum ), $iStartPos + 8, $sVATSum );
            $iCtr++;
        }

        // products brutto price
        $sBrutPrice = $oLang->formatCurrency( $this->_oData->oxorder__oxtotalbrutsum->value, $this->_oData->getCurrency() ).' '.$this->_oData->getCurrency()->name;
        $this->text( 45, $iStartPos + 12, $this->_oData->translate( 'ORDER_OVERVIEW_PDF_ALLPRICEBRUTTO' ) );
        $this->text( 195 - $this->_oPdf->getStringWidth( $sBrutPrice ), $iStartPos + 12, $sBrutPrice );
        $iStartPos++;

        // line separator
        $this->line( 45, $iStartPos + 13, 195, $iStartPos + 13 );
        $iStartPos += 5;
    }

    /**
     * Sets total costs values using order with discount
     *
     * @param int &$iStartPos text start position
     *
     * @return none
     */
    protected function _setTotalCostsWithDiscount( &$iStartPos )
    {
        $oLang = oxLang::getInstance();

        // line separator
        $this->line( 15, $iStartPos + 1, 195, $iStartPos + 1 );

        // products brutto price
        $sBrutPrice = $oLang->formatCurrency( $this->_oData->oxorder__oxtotalbrutsum->value, $this->_oData->getCurrency() ).' '.$this->_oData->getCurrency()->name;
        $this->text( 45, $iStartPos + 4, $this->_oData->translate( 'ORDER_OVERVIEW_PDF_ALLPRICEBRUTTO' ) );
        $this->text( 195 - $this->_oPdf->getStringWidth( $sBrutPrice ), $iStartPos + 4, $sBrutPrice );

        // line separator
        $this->line( 45, $iStartPos + 5, 195, $iStartPos + 5 );

        // discount
        $dDiscountVal = $this->_oData->oxorder__oxdiscount->value;
        if ( $dDiscountVal > 0 ) {
            $dDiscountVal *= -1;
        }
        $sDiscount = $oLang->formatCurrency( $dDiscountVal, $this->_oData->getCurrency() ).' '.$this->_oData->getCurrency()->name;
        $this->text( 45, $iStartPos + 8, $this->_oData->translate( 'ORDER_OVERVIEW_PDF_DISCOUNT' ) );
        $this->text( 195 - $this->_oPdf->getStringWidth( $sDiscount ), $iStartPos + 8, $sDiscount );
        $iStartPos++;

        // line separator
        $this->line( 45, $iStartPos + 8, 195, $iStartPos + 8 );
        $iStartPos += 4;

        // products netto price
        $sNetSum = $oLang->formatCurrency( $this->_oData->oxorder__oxtotalnetsum->value, $this->_oData->getCurrency() ).' '.$this->_oData->getCurrency()->name;
        $this->text( 45, $iStartPos + 8, $this->_oData->translate( 'ORDER_OVERVIEW_PDF_ALLPRICENETTO' ) );
        $this->text( 195 - $this->_oPdf->getStringWidth( $sNetSum ), $iStartPos + 8, $sNetSum );

        // #345 - product VAT info
        $iCtr = 0;
        foreach ( $this->_oData->getVats() as $iVat => $dVatPrice ) {
            $iStartPos += 4 * $iCtr;
            $sVATSum = $oLang->formatCurrency( $dVatPrice, $this->_oData->getCurrency() ).' '.$this->_oData->getCurrency()->name;
            $this->text( 45, $iStartPos + 12, $this->_oData->translate( 'ORDER_OVERVIEW_PDF_ZZGLVAT' ).$iVat.$this->_oData->translate('ORDER_OVERVIEW_PDF_PERCENTSUM' ) );
            $this->text( 195 - $this->_oPdf->getStringWidth( $sVATSum ), $iStartPos + 12, $sVATSum );
            $iCtr++;
        }
        $iStartPos += 4;
    }

    /**
     * Sets voucher values to pdf
     *
     * @param int &$iStartPos text start position
     *
     * @return none
     */
    protected function _setVoucherInfo( &$iStartPos )
    {
        $dVoucher = 0;
        if ( $this->_oData->oxorder__oxvoucherdiscount->value ) {
            $dDiscountVal = $this->_oData->oxorder__oxvoucherdiscount->value;
            if ( $dDiscountVal > 0 ) {
                $dDiscountVal *= -1;
            }
            $sPayCost = oxLang::getInstance()->formatCurrency( $dDiscountVal, $this->_oData->getCurrency() ).' '.$this->_oData->getCurrency()->name;
            $this->text( 45, $iStartPos, $this->_oData->translate( 'ORDER_OVERVIEW_PDF_VOUCHER' ) );
            $this->text( 195 - $this->_oPdf->getStringWidth( $sPayCost ), $iStartPos, $sPayCost );
            $iStartPos += 4;
        }

        $iStartPos++;
    }

    /**
     * Sets delivery info to pdf
     *
     * @param int &$iStartPos text start position
     *
     * @return none
     */
    protected function _setDeliveryInfo( &$iStartPos )
    {
        $sAddString = '';
        $oLang = oxLang::getInstance();
        $oConfig = oxConfig::getInstance();
        if ( $oConfig->getConfigParam( 'blCalcVATForDelivery' ) ) {
            // delivery netto
            $sDelCostNetto = $oLang->formatCurrency( $this->_oData->getOrderDeliveryPrice()->getNettoPrice(), $this->_oData->getCurrency() ).' '.$this->_oData->getCurrency()->name;
            $this->text( 45, $iStartPos, $this->_oData->translate( 'ORDER_OVERVIEW_PDF_SHIPCOST' ).' '.$this->_oData->translate('ORDER_OVERVIEW_PDF_NETTO' ) );
            $this->text( 195 - $this->_oPdf->getStringWidth( $sDelCostNetto ), $iStartPos, $sDelCostNetto );
            $iStartPos += 4;

            // delivery VAT
            $sDelCostVAT = $oLang->formatCurrency( $this->_oData->getOrderDeliveryPrice()->getVATValue(), $this->_oData->getCurrency()).' '.$this->_oData->getCurrency()->name;
            $this->text( 45, $iStartPos, $this->_oData->translate( 'ORDER_OVERVIEW_PDF_ZZGLVAT' ).$this->_oData->oxorder__oxdelvat->value.$this->_oData->translate( 'ORDER_OVERVIEW_PDF_PERCENTSUM' ) );
            $this->text( 195 - $this->_oPdf->getStringWidth( $sDelCostVAT ), $iStartPos, $sDelCostVAT );
            //$iStartPos += 4;

            $sAddString = ' '.$this->_oData->translate( 'ORDER_OVERVIEW_PDF_BRUTTO' );
        } else {
            // if canceled order, reset value
            if ( $this->_oData->oxorder__oxstorno->value ) {
                $this->_oData->oxorder__oxdelcost->setValue(0);
            }

            $sDelCost = $oLang->formatCurrency( $this->_oData->oxorder__oxdelcost->value, $this->_oData->getCurrency() ).' '.$this->_oData->getCurrency()->name;
            $this->text( 45, $iStartPos, $this->_oData->translate( 'ORDER_OVERVIEW_PDF_SHIPCOST' ).$sAddString );
            $this->text( 195 - $this->_oPdf->getStringWidth( $sDelCost ), $iStartPos, $sDelCost );
        }
    }

    /**
     * Sets wrapping info to pdf
     *
     * @param int &$iStartPos text start position
     *
     * @return none
     */
    protected function _setWrappingInfo( &$iStartPos )
    {
        if ( $this->_oData->oxorder__oxwrapcost->value ) {
            $sAddString = '';
            $oLang = oxLang::getInstance();

            //displaying wrapping VAT info
            if ( $this->_oData->oxorder__oxwrapvat->value ) {

                // wrapping netto
                $iStartPos += 5;
                $sWrapCostNetto = $oLang->formatCurrency( $this->_oData->getOrderWrappingPrice()->getNettoPrice(), $this->_oData->getCurrency()).' '.$this->_oData->getCurrency()->name;
                $this->text( 45, $iStartPos, $this->_oData->translate( 'ORDER_OVERVIEW_PDF_WRAPPING' ).' '.$this->_oData->translate( 'ORDER_OVERVIEW_PDF_NETTO' ) );
                $this->text( 195 - $this->_oPdf->getStringWidth( $sWrapCostNetto ), $iStartPos, $sWrapCostNetto );

                //wrapping VAT
                $iStartPos += 4;
                $sWrapCostVAT = $oLang->formatCurrency( $this->_oData->getOrderWrappingPrice()->getVATValue(), $this->_oData->getCurrency()).' '.$this->_oData->getCurrency()->name;
                $this->text( 45, $iStartPos, $this->_oData->translate( 'ORDER_OVERVIEW_PDF_ZZGLVAT' ).$this->_oData->oxorder__oxwrapvat->value.$this->_oData->translate( 'ORDER_OVERVIEW_PDF_PERCENTSUM' ) );
                $this->text( 195 - $this->_oPdf->getStringWidth( $sWrapCostVAT ), $iStartPos, $sWrapCostVAT );

                $sAddString = ' '.$this->_oData->translate( 'ORDER_OVERVIEW_PDF_BRUTTO' );

                // drawing line separator
                $iStartPos += 4;
                $this->line(45, $iStartPos - 3, 195, $iStartPos - 3);
            }
            $iStartPos += 4;

            // wrapping cost
            $sWrapCost = $oLang->formatCurrency( $this->_oData->oxorder__oxwrapcost->value, $this->_oData->getCurrency() ).' '.$this->_oData->getCurrency()->name;
            $this->text( 45, $iStartPos, $this->_oData->translate( 'ORDER_OVERVIEW_PDF_WRAPPING' ).$sAddString );
            $this->text( 195 - $this->_oPdf->getStringWidth( $sWrapCost ), $iStartPos, $sWrapCost );
            $iStartPos++;
        }
    }

    /**
     * Sets payment info to pdf
     *
     * @param int &$iStartPos text start position
     *
     * @return none
     */
    protected function _setPaymentInfo( &$iStartPos )
    {
        $oLang = oxLang::getInstance();
        if ( $this->_oData->oxorder__oxpayvat->value ) {

            // payment netto
            $iStartPos += 4;
            $sPayCostNetto = $oLang->formatCurrency( $this->_oData->getOrderPaymentPrice()->getNettoPrice(), $this->_oData->getCurrency() ).' '.$this->_oData->getCurrency()->name;
            $this->text( 45, $iStartPos, $this->_oData->translate( 'ORDER_OVERVIEW_PDF_PAYMENTIMPACT' ).' '.$this->_oData->translate( 'ORDER_OVERVIEW_PDF_NETTO' ) );
            $this->text( 195 - $this->_oPdf->getStringWidth( $sPayCostNetto ), $iStartPos, $sPayCostNetto );

            // payment VAT
            $iStartPos += 4;
            $sPayCostVAT = $oLang->formatCurrency( $this->_oData->getOrderPaymentPrice()->getVATValue(), $this->_oData->getCurrency() ).' '.$this->_oData->getCurrency()->name;
            $this->text( 45, $iStartPos, $this->_oData->translate( 'ORDER_OVERVIEW_PDF_ZZGLVAT' ).$this->_oData->oxorder__oxpayvat->value.$this->_oData->translate( 'ORDER_OVERVIEW_PDF_PERCENTSUM' ) );
            $this->text( 195 - $this->_oPdf->getStringWidth( $sPayCostVAT ), $iStartPos, $sPayCostVAT );

        }

        // if canceled order, reset value
        if ( $this->_oData->oxorder__oxstorno->value ) {
            $this->_oData->oxorder__oxpaycost->setValue(0);
        }

        // payment costs
        if ( $this->_oData->oxorder__oxpaycost->value ) {
            $iStartPos += 4;
            $sPayCost = $oLang->formatCurrency( $this->_oData->oxorder__oxpaycost->value, $this->_oData->getCurrency() ).' '.$this->_oData->getCurrency()->name;
            $this->text( 45, $iStartPos, $this->_oData->translate( 'ORDER_OVERVIEW_PDF_PAYMENTIMPACT' ) );
            $this->text( 195 - $this->_oPdf->getStringWidth( $sPayCost ), $iStartPos, $sPayCost );
        }

        $iStartPos++;
    }

    /**
     * Sets payment info to pdf
     *
     * @param int &$iStartPos text start position
     *
     * @return none
     */
    protected function _setTsProtection( &$iStartPos )
    {
        $oLang   = oxLang::getInstance();
        $oConfig = oxConfig::getInstance();
        if ( $this->_oData->oxorder__oxtsprotectcosts->value && $oConfig->getConfigParam( 'blCalcVATForPayCharge' ) ) {

            // payment netto
            $iStartPos += 4;
            $sPayCostNetto = $oLang->formatCurrency( $this->_oData->getOrderTsProtectionPrice()->getNettoPrice(), $this->_oData->getCurrency() ).' '.$this->_oData->getCurrency()->name;
            $this->text( 45, $iStartPos, $this->_oData->translate( 'ORDER_OVERVIEW_PDF_TSPROTECTION' ).' '.$this->_oData->translate( 'ORDER_OVERVIEW_PDF_NETTO' ) );
            $this->text( 195 - $this->_oPdf->getStringWidth( $sPayCostNetto ), $iStartPos, $sPayCostNetto );

            // payment VAT
            $iStartPos += 4;
            $sPayCostVAT = $oLang->formatCurrency( $this->_oData->getOrderTsProtectionPrice()->getVATValue(), $this->_oData->getCurrency() ).' '.$this->_oData->getCurrency()->name;
            $this->text( 45, $iStartPos, $this->_oData->translate( 'ORDER_OVERVIEW_PDF_ZZGLVAT' ).$oConfig->getConfigParam( 'dDefaultVAT' ).$this->_oData->translate( 'ORDER_OVERVIEW_PDF_PERCENTSUM' ) );
            $this->text( 195 - $this->_oPdf->getStringWidth( $sPayCostVAT ), $iStartPos, $sPayCostVAT );

            $iStartPos++;

        } elseif ( $this->_oData->oxorder__oxtsprotectcosts->value ) {

            $iStartPos += 4;
            $sPayCost = $oLang->formatCurrency( $this->_oData->oxorder__oxtsprotectcosts->value, $this->_oData->getCurrency() ).' '.$this->_oData->getCurrency()->name;
            $this->text( 45, $iStartPos, $this->_oData->translate( 'ORDER_OVERVIEW_PDF_TSPROTECTION' ) );
            $this->text( 195 - $this->_oPdf->getStringWidth( $sPayCost ), $iStartPos, $sPayCost );

            $iStartPos++;
        }
    }

    /**
     * Sets grand total order price to pdf
     *
     * @param int &$iStartPos text start position
     *
     * @return none
     */
    protected function _setGrandTotalPriceInfo( &$iStartPos )
    {
        $oLang = oxLang::getInstance();

        // total netto price (A. very unprecise, impossible to count because of many factors)
        $this->font( $this->getFont(), 'B', 10 );
        if ( $this->_oData->oxorder__oxdelvat->value || $this->_oData->oxorder__oxwrapvat->value || $this->_oData->oxorder__oxpayvat->value ) {

            // collecting total net price
            $sTotalNetSum = $oLang->formatCurrency( $this->_oData->getOrderNetSum(), $this->_oData->getCurrency() ).' '.$this->_oData->getCurrency()->name;
            $this->text( 45, $iStartPos, $this->_oData->translate( 'ORDER_OVERVIEW_PDF_ALLSUM_NET' ) );
            $this->text( 195 - $this->_oPdf->getStringWidth( $sTotalNetSum ), $iStartPos, $sTotalNetSum );
            $iStartPos += 4;
        }

        // total order sum
        $sTotalOrderSum = $oLang->formatCurrency( $this->_oData->oxorder__oxtotalordersum->value, $this->_oData->getCurrency() ).' '.$this->_oData->getCurrency()->name;
        $this->text( 45, $iStartPos, $this->_oData->translate( 'ORDER_OVERVIEW_PDF_ALLSUM' ) );
        $this->text( 195 - $this->_oPdf->getStringWidth( $sTotalOrderSum ), $iStartPos, $sTotalOrderSum );
        $iStartPos += 2;
    }

    /**
     * Sets payment method info to pdf
     *
     * @param int &$iStartPos text start position
     *
     * @return none
     */
    protected function _setPaymentMethodInfo( &$iStartPos )
    {
        $oPayment = oxNew( 'oxpayment' );
        $oPayment->loadInLang( $this->_oData->getSelectedLang(), $this->_oData->oxorder__oxpaymenttype->value );

        $text = $this->_oData->translate( 'ORDER_OVERVIEW_PDF_SELPAYMENT' ).$oPayment->oxpayments__oxdesc->value;
        $this->font( $this->getFont(), '', 10 );
        $this->text( 15, $iStartPos + 4, $text );
        $iStartPos += 4;
    }

    /**
     * Sets pay until date to pdf
     *
     * @param int &$iStartPos text start position
     *
     * @return none
     */
    protected function _setPayUntilInfo( &$iStartPos )
    {
        $text = $this->_oData->translate( 'ORDER_OVERVIEW_PDF_PAYUPTO' ).date( 'd.m.Y', mktime( 0, 0, 0, date ( 'm' ), date ( 'd' ) + 7, date( 'Y' ) ) );
        $this->font( $this->getFont(), '', 10 );
        $this->text( 15, $iStartPos + 4, $text );
        $iStartPos += 4;
    }

    /**
     * Generates order info block (prices, VATs, etc )
     *
     * @param int $iStartPos text start position
     *
     * @return int
     */
    public function generate( $iStartPos )
    {

        $this->font( $this->getFont(), '', 10 );
        $siteH = $iStartPos;

        // #1147 discount for vat must be displayed
        if ( !$this->_oData->oxorder__oxdiscount->value ) {
            $this->_setTotalCostsWithoutDiscount( $siteH );
        } else {
            $this->_setTotalCostsWithDiscount( $siteH );
        }

        $siteH += 12;

        // voucher info
        $this->_setVoucherInfo( $siteH );

        // additional line separator
        if ( $this->_oData->oxorder__oxdiscount->value || $this->_oData->oxorder__oxvoucherdiscount->value ) {
            $this->line( 45, $siteH - 3, 195, $siteH - 3 );
        }

        // delivery info
        $this->_setDeliveryInfo( $siteH );

        // wrapping info
        $this->_setWrappingInfo( $siteH );

        // payment info
        $this->_setPaymentInfo( $siteH );

        // TS protection info
        $this->_setTsProtection( $siteH );

        // separating line
        $this->line( 15, $siteH, 195, $siteH );
        $siteH += 4;

        // total order sum
        $this->_setGrandTotalPriceInfo( $siteH );

        // separating line
        $this->line( 15, $siteH, 195, $siteH );
        $siteH += 4;

        // payment method
        $this->_setPaymentMethodInfo( $siteH );

        // pay until ...
        $this->_setPayUntilInfo( $siteH );

        return $siteH - $iStartPos;
    }
}

/**
 * Order pdf generator class
 */
class MyOrder extends MyOrder_parent
{

    /**
     * PDF language
     * @var int
     */
    protected $_iSelectedLang = 0;

    /**
     * Cached active shop object
     * @var object
     */
    protected $_oActShop = null;

    /**
     * Order arctiles VAT's
     * @var array
     */
    protected $_aVATs = array();

    /**
     * Order currency object
     * @var object
     */
    protected $_oCur = null;

    /**
     * Returns active shop object
     *
     * @return oxshop $oUser
     */
    protected function _getActShop()
    {
        // shop is allready loaded
        if ( $this->_oActShop !== null )
            return $this->_oActShop;

        $this->_oActShop = oxNew( 'oxshop' );
        $this->_oActShop->load( $this->getConfig()->getShopId() );

        return $this->_oActShop;
    }

    /**
     * Returns translated string
     *
     * @param string $sString string to translate
     *
     * @return string
     */
    public function translate( $sString )
    {
        return oxLang::getInstance()->translateString( $sString, $this->_iSelectedLang );
    }

    /**
     * Formats pdf page footer
     *
     * @param object $oPdf pdf document object
     *
     * @return null
     */
    public function pdfFooter( $oPdf )
    {

        $oShop = $this->_getActShop();

        $oPdf->line( 15, 272, 195, 272 );

        $oPdfBlock = new PdfBlock();
        /* column 1 - company name, shop owner info, shop address */
        $oPdf->setFont( $oPdfBlock->getFont(), '', 7 );
        $oPdf->text( 15, 275, strip_tags( $oShop->oxshops__oxcompany->getRawValue() ) );
        $oPdf->text( 15, 278, strip_tags( $oShop->oxshops__oxfname->getRawValue() ).' '. strip_tags( $oShop->oxshops__oxlname->getRawValue() ) );
        $oPdf->text( 15, 281, strip_tags( $oShop->oxshops__oxstreet->getRawValue() ) );
        $oPdf->text( 15, 284, strip_tags( $oShop->oxshops__oxzip->value ).' '. strip_tags( $oShop->oxshops__oxcity->getRawValue() ) );
        $oPdf->text( 15, 287, strip_tags( $oShop->oxshops__oxcountry->getRawValue() ) );

        /* column 2 - phone, fax, url, email address */
        $oPdf->text( 85, 275, $this->translate( 'ORDER_OVERVIEW_PDF_PHONE' ).strip_tags( $oShop->oxshops__oxtelefon->value ) );
        $oPdf->text( 85, 278, $this->translate( 'ORDER_OVERVIEW_PDF_FAX' ).strip_tags( $oShop->oxshops__oxtelefax->value ) );
        $oPdf->text( 85, 281, strip_tags( $oShop->oxshops__oxurl->value ) );
        $oPdf->text( 85, 284, strip_tags( $oShop->oxshops__oxorderemail->value ) );

        /* column 3 - bank information */
        $oPdf->text( 150, 275, strip_tags( $oShop->oxshops__oxbankname->getRawValue() ) );
        $oPdf->text( 150, 278, $this->translate( 'ORDER_OVERVIEW_PDF_ACCOUNTNR' ).strip_tags( $oShop->oxshops__oxbanknumber->value ) );
        $oPdf->text( 150, 281, $this->translate( 'ORDER_OVERVIEW_PDF_BANKCODE' ).strip_tags( $oShop->oxshops__oxbankcode->value ) );
        $oPdf->text( 150, 284, strip_tags( $oShop->oxshops__oxvatnumber->value ) );
        $oPdf->text( 150, 287, '' );
    }

    /**
     * Adds shop logo to page header. Returns position for next texts in pdf
     *
     * @param object $oPdf pdf document object
     *
     * @return int
     */
    public function pdfHeaderPlus( $oPdf )
    {

        // new page with shop logo
        $this->pdfHeader( $oPdf );

        $oPdfBlock = new PdfBlock();
        // column names
        $oPdf->setFont( $oPdfBlock->getFont(), '', 8 );
        $oPdf->text( 15, 50, $this->translate( 'ORDER_OVERVIEW_PDF_AMOUNT' ) );
        $oPdf->text( 30, 50, $this->translate( 'ORDER_OVERVIEW_PDF_ARTID') );
        $oPdf->text( 45, 50, $this->translate( 'ORDER_OVERVIEW_PDF_DESC' ) );
        $oPdf->text( 160, 50, $this->translate( 'ORDER_OVERVIEW_PDF_UNITPRICE' ) );
        $sText = $this->translate( 'ORDER_OVERVIEW_PDF_ALLPRICE' );
        $oPdf->text( 195 - $oPdf->getStringWidth( $sText ), 50, $sText );

        // line separator
        $oPdf->line( 15, 52, 195, 52 );

        return 56;
    }

    /**
     * Creating new page with shop logo. Returning position to continue data writing
     *
     * @param object $oPdf pdf document object
     *
     * @return int
     */
    public function pdfHeader( $oPdf )
    {
        // adding new page ...
        $oPdf->addPage();

        // loading active shop
        $oShop = $this->_getActShop();

        //logo
        $myConfig = $this->getConfig();
        $aSize    = getimagesize( $myConfig->getImageDir().'/pdf_logo.jpg' );
        $iMargin  = 195 - $aSize[0] * 0.2;
        $oPdf->setLink( $oShop->oxshops__oxurl->value );
        $oPdf->image( $myConfig->getImageDir().'/pdf_logo.jpg', $iMargin, 10, $aSize[0] * 0.2, $aSize[1] * 0.2, '', $oShop->oxshops__oxurl->value );
        return 14 + $aSize[1] * 0.2;
    }

    /**
     * Generates order pdf report file
     *
     * @param string $sFilename name of report file
     * @param int    $iSelLang  active language
     *
     * @return null
     */
    public function genPdf( $sFilename, $iSelLang = 0 )
    {
        // setting pdf language
        $this->_iSelectedLang = $iSelLang;

        // setting invoice number
        if ( !$this->oxorder__oxbillnr->value ) {
            $this->oxorder__oxbillnr->setValue($this->getNextBillNum());
            $this->save();
        }

        // initiating pdf engine
        $oPdf = oxNew( 'oxPDF' );
        $oPdf->setPrintHeader( false );
        $oPdf->open();

        // adding header
        $this->pdfHeader( $oPdf );

        // adding info data
        switch ( oxConfig::getParameter( 'pdftype' ) ) {
            case 'dnote':
                $this->exportDeliveryNote( $oPdf );
                break;
            default:
                $this->exportStandart( $oPdf );
        }

        // adding footer
        $this->pdfFooter( $oPdf );

        // outputting file to browser
        $oPdf->output( $sFilename, 'I' );
    }


    /**
     * Set billing address info to pdf
     *
     * @param object $oPdf pdf document object
     *
     * @return null
     */
    protected function _setBillingAddressToPdf( $oPdf )
    {
        $oLang = oxLang::getInstance();
        $sSal = $this->oxorder__oxbillsal->value;
        try {
            $sSal = $oLang->translateString($this->oxorder__oxbillsal->value, $this->_iSelectedLang);
        } catch (Exception $e) {
        }
        $oPdfBlock = new PdfBlock();
        $oPdf->setFont( $oPdfBlock->getFont(), '', 10 );
        $oPdf->text( 15, 59, $sSal);
        $oPdf->text( 15, 63, $this->oxorder__oxbillfname->getRawValue().' '.$this->oxorder__oxbilllname->getRawValue() );
        $oPdf->text( 15, 67, $this->oxorder__oxbillcompany->getRawValue() );
        $oPdf->text( 15, 71, $this->oxorder__oxbillstreet->getRawValue().' '.$this->oxorder__oxbillstreetnr->value );
        $oPdf->setFont( $oPdfBlock->getFont(), 'B', 10 );
        $oPdf->text( 15, 75, $this->oxorder__oxbillzip->value.' '.$this->oxorder__oxbillcity->getRawValue() );
        $oPdf->setFont( $oPdfBlock->getFont(), '', 10 );
        $oPdf->text( 15, 79, $this->oxorder__oxbillcountry->getRawValue() );
    }

    /**
     * Set delivery address info to pdf
     *
     * @param object $oPdf pdf document object
     *
     * @return null
     */
    protected function _setDeliveryAddressToPdf( $oPdf )
    {
        $oLang = oxLang::getInstance();
        $sSal = $this->oxorder__oxdelsal->value;
        try {
            $sSal = $oLang->translateString($this->oxorder__oxdelsal->value, $this->_iSelectedLang);
        } catch (Exception $e) {
        }
        $oPdfBlock = new PdfBlock();
        $oPdf->setFont( $oPdfBlock->getFont(), '', 6 );
        $oPdf->text( 15, 87, $this->translate( 'ORDER_OVERVIEW_PDF_DELIVERYADDRESS' ) );
        $oPdf->setFont( $oPdfBlock->getFont(), '', 10 );
        $oPdf->text( 15, 91, $sSal);
        $oPdf->text( 15, 95, $this->oxorder__oxdellname->getRawValue().' '.$this->oxorder__oxdelfname->getRawValue() );
        $oPdf->text( 15, 99, $this->oxorder__oxdelcompany->getRawValue() );
        $oPdf->text( 15, 103, $this->oxorder__oxdelstreet->getRawValue().' '.$this->oxorder__oxdelstreetnr->value );
        $oPdf->setFont( $oPdfBlock->getFont(), 'B', 10 );
        $oPdf->text( 15, 107, $this->oxorder__oxdelzip->value.' '.$this->oxorder__oxdelcity->getRawValue() );
        $oPdf->setFont( $oPdfBlock->getFont(), '', 10 );
        $oPdf->text( 15, 111, $this->oxorder__oxdelcountry->getRawValue() );
    }

    /**
     * Set order articles info and articles VAT's to pdf
     *
     * @param object $oPdf        pdf document object
     * @param int    &$iStartPos  text start position from top
     * @param bool   $blShowPrice show articles prices / VAT info or not
     *
     * @return null
     */
    protected function _setOrderArticlesToPdf( $oPdf, &$iStartPos, $blShowPrice = true )
    {
        if (!$this->_oArticles) {
            $this->_oArticles = $this->getOrderArticles(true);
        }

        $oCurr = $this->getCurrency();
        $oPdfBlock = new PdfBlock();
        // product list
        foreach ( $this->_oArticles as $key => $oOrderArt ) {

            // starting a new page ...
            if ( $iStartPos > 243 ) {
                $this->pdffooter( $oPdf );
                $iStartPos = $this->pdfheaderplus( $oPdf );
                $oPdf->setFont( $oPdfBlock->getFont(), '', 10 );
            } else {
                $iStartPos = $iStartPos + 4;
            }

            // sold amount
            $oPdf->text( 20 - $oPdf->getStringWidth( $oOrderArt->oxorderarticles__oxamount->value ), $iStartPos, $oOrderArt->oxorderarticles__oxamount->value );

            // product number
            $oPdf->setFont( $oPdfBlock->getFont(), '', 8 );
            $oPdf->text( 28, $iStartPos, $oOrderArt->oxorderarticles__oxartnum->value );

            // product title
            $oPdf->setFont( $oPdfBlock->getFont(), '', 10 );
            $oPdf->text( 45, $iStartPos, substr( strip_tags( $this->_replaceExtendedChars( $oOrderArt->oxorderarticles__oxtitle->getRawValue(), true ) ), 0, 58 ) );

            if ( $blShowPrice ) {
                $oLang = oxLang::getInstance();

                // calculating product VAT info
                if ( !isset( $this->_aVATs[$oOrderArt->oxorderarticles__oxvat->value] ) ) {
                    $this->_aVATs[$oOrderArt->oxorderarticles__oxvat->value] = 0;
                }
                // #M1434: VAT recalculation in admin (pdf) is wrong
                if ( $oOrderArt->oxorderarticles__oxamount->value > 0 ) {
                    $this->_aVATs[$oOrderArt->oxorderarticles__oxvat->value] += ( $oOrderArt->oxorderarticles__oxvatprice->value * $oCurr->rate );
                }

                // product VAT percent
                $oPdf->text( 150 - $oPdf->getStringWidth( $oOrderArt->oxorderarticles__oxvat->value ), $iStartPos, $oOrderArt->oxorderarticles__oxvat->value );

                // product price
                $sText = $oLang->formatCurrency( $oOrderArt->oxorderarticles__oxbprice->value, $this->_oCur ).' '.$this->_oCur->name;
                $oPdf->text( 173 - $oPdf->getStringWidth( $sText ), $iStartPos, $sText );

                // total product price
                $sText = $oLang->formatCurrency( $oOrderArt->oxorderarticles__oxbrutprice->value, $this->_oCur ).' '.$this->_oCur->name;
                $oPdf->text( 195 - $oPdf->getStringWidth( $sText ), $iStartPos, $sText );

            }

            // additional variant info
            if ( $oOrderArt->oxorderarticles__oxselvariant->value ) {
                $iStartPos = $iStartPos + 4;
                $oPdf->text( 45, $iStartPos, substr( $oOrderArt->oxorderarticles__oxselvariant->value, 0, 58 ) );
            }

        }
    }

    /**
     * Exporting standard invoice pdf
     *
     * @param object $oPdf pdf document object
     *
     * @return null
     */
    public function exportStandart( $oPdf )
    {
        // preparing order curency info
        $myConfig = $this->getConfig();
        $oPdfBlock = new PdfBlock();

        $this->_oCur = $myConfig->getCurrencyObject( $this->oxorder__oxcurrency->value );
        if ( !$this->_oCur ) {
            $this->_oCur = $myConfig->getActShopCurrencyObject();
        }

        // loading active shop
        $oShop = $this->_getActShop();

        // shop information
        $oPdf->setFont( $oPdfBlock->getFont(), '', 6 );
        $oPdf->text( 15, 55, $oShop->oxshops__oxname->getRawValue().' - '.$oShop->oxshops__oxstreet->getRawValue().' - '.$oShop->oxshops__oxzip->value.' - '.$oShop->oxshops__oxcity->getRawValue() );

        // billing address
        $this->_setBillingAddressToPdf( $oPdf );

        // delivery address
        if ( $this->oxorder__oxdelsal->value ) {
            $this->_setDeliveryAddressToPdf( $oPdf );
        }

        // loading user
        $oUser = oxNew( 'oxuser' );
        $oUser->load( $this->oxorder__oxuserid->value );

        // user info
        $sText = $this->translate( 'ORDER_OVERVIEW_PDF_FILLONPAYMENT' );
        $oPdf->setFont( $oPdfBlock->getFont(), '', 5 );
        $oPdf->text( 195 - $oPdf->getStringWidth( $sText ), 55, $sText );

        // customer number
        $sCustNr = $this->translate( 'ORDER_OVERVIEW_PDF_CUSTNR').' '.$oUser->oxuser__oxcustnr->value;
        $oPdf->setFont( $oPdfBlock->getFont(), '', 7 );
        $oPdf->text( 195 - $oPdf->getStringWidth( $sCustNr ), 59, $sCustNr );

        // setting position if delivery address is used
        if ( $this->oxorder__oxdelsal->value ) {
            $iTop = 115;
        } else {
            $iTop = 91;
        }

        // shop city
        $sText = $oShop->oxshops__oxcity->getRawValue().', '.date( 'd.m.Y' );
        $oPdf->setFont( $oPdfBlock->getFont(), '', 10 );
        $oPdf->text( 195 - $oPdf->getStringWidth( $sText ), $iTop + 8, $sText );

        // shop VAT number
        if ( $oShop->oxshops__oxvatnumber->value ) {
            $sText = $this->translate( 'ORDER_OVERVIEW_PDF_TAXIDNR' ).' '.$oShop->oxshops__oxvatnumber->value;
            $oPdf->text( 195 - $oPdf->getStringWidth( $sText ), $iTop + 12, $sText );
            $iTop += 8;
        } else {
            $iTop += 4;
        }

        // invoice number
        $sText = $this->translate( 'ORDER_OVERVIEW_PDF_COUNTNR' ).' '.$this->oxorder__oxbillnr->value;
        $oPdf->text( 195 - $oPdf->getStringWidth( $sText ), $iTop + 8, $sText );

        // marking if order is canceled
        if ( $this->oxorder__oxstorno->value == 1 ) {
            $this->oxorder__oxordernr->setValue( $this->oxorder__oxordernr->getRawValue() . '   '.$this->translate( 'ORDER_OVERVIEW_PDF_STORNO' ), oxField::T_RAW );
        }

        // order number
        $oPdf->setFont( $oPdfBlock->getFont(), '', 12 );
        $oPdf->text( 15, $iTop, $this->translate( 'ORDER_OVERVIEW_PDF_PURCHASENR' ).' '.$this->oxorder__oxordernr->value );

        // order date
        $oPdf->setFont( $oPdfBlock->getFont(), '', 10 );
        $aOrderDate = explode( ' ', $this->oxorder__oxorderdate->value );
        $sOrderDate = oxUtilsDate::getInstance()->formatDBDate( $aOrderDate[0]);
        $oPdf->text( 15, $iTop + 8, $this->translate( 'ORDER_OVERVIEW_PDF_ORDERSFROM' ).$sOrderDate.$this->translate( 'ORDER_OVERVIEW_PDF_ORDERSAT' ).$oShop->oxshops__oxurl->value );
        $iTop += 16;

        // product info header
        $oPdf->setFont( $oPdfBlock->getFont(), '', 8 );
        $oPdf->text( 15, $iTop, $this->translate( 'ORDER_OVERVIEW_PDF_AMOUNT' ) );
        $oPdf->text( 30, $iTop, $this->translate( 'ORDER_OVERVIEW_PDF_ARTID' ) );
        $oPdf->text( 45, $iTop, $this->translate( 'ORDER_OVERVIEW_PDF_DESC' ) );
        $oPdf->text( 145, $iTop, $this->translate( 'ORDER_OVERVIEW_PDF_VAT' ) );
        $oPdf->text( 158, $iTop, $this->translate( 'ORDER_OVERVIEW_PDF_UNITPRICE' ) );
        $sText = $this->translate( 'ORDER_OVERVIEW_PDF_ALLPRICE' );
        $oPdf->text( 195 - $oPdf->getStringWidth( $sText ), $iTop, $sText );

        // separator line
        $iTop += 2;
        $oPdf->line( 15, $iTop, 195, $iTop );

        // #345
        $siteH = $iTop;
        $oPdf->setFont( $oPdfBlock->getFont(), '', 10 );

        // order articles
        $this->_setOrderArticlesToPdf( $oPdf, $siteH, true );

        // generating pdf file
        $oArtSumm = new PdfArticleSummary( $this, $oPdf );
        $iHeight = $oArtSumm->generate( $siteH );
        if ( $siteH + $iHeight > 258 ) {
            $this->pdfFooter( $oPdf );
            $iTop = $this->pdfHeader( $oPdf );
            $oArtSumm->ajustHeight( $iTop - $siteH );
            $siteH = $iTop;
        }

        $oArtSumm->run( $oPdf );
        $siteH += $iHeight + 8;

        $oPdf->text( 15, $siteH, $this->translate( 'ORDER_OVERVIEW_PDF_GREETINGS' ) );
    }

    /**
     * Generating delivery note pdf
     *
     * @param object $oPdf pdf document object
     *
     * @return null
     */
    public function exportDeliveryNote( $oPdf )
    {
        $myConfig = $this->getConfig();
        $oShop    = $this->_getActShop();
        $oPdfBlock = new PdfBlock();

        $oLang = oxLang::getInstance();
        $sSal = $this->oxorder__oxdelsal->value;
        try {
            $sSal = $oLang->translateString($this->oxorder__oxdelsal->value, $this->_iSelectedLang);
        } catch (Exception $e) {
        }

        // loading order currency info
        $this->_oCur = $myConfig->getCurrencyObject( $this->oxorder__oxcurrency->value );
        if ( !isset( $this->_oCur ) ) {
            $this->_oCur = $myConfig->getActShopCurrencyObject();
        }

        // shop info
        $oPdf->setFont( $oPdfBlock->getFont(), '', 6 );
        $oPdf->text( 15, 55, $oShop->oxshops__oxname->getRawValue().' - '.$oShop->oxshops__oxstreet->getRawValue().' - '.$oShop->oxshops__oxzip->value.' - '.$oShop->oxshops__oxcity->getRawValue() );

        // delivery address
        $oPdf->setFont( $oPdfBlock->getFont(), '', 10 );
        if ( $this->oxorder__oxdelsal->value ) {
            $oPdf->text( 15, 59, $sSal );
            $oPdf->text( 15, 63, $this->oxorder__oxdellname->getRawValue().' '.$this->oxorder__oxdelfname->getRawValue() );
            $oPdf->text( 15, 67, $this->oxorder__oxdelcompany->getRawValue() );
            $oPdf->text( 15, 71, $this->oxorder__oxdelstreet->getRawValue().' '.$this->oxorder__oxdelstreetnr->value );
            $oPdf->setFont( $oPdfBlock->getFont(), 'B', 10 );
            $oPdf->text( 15, 75, $this->oxorder__oxdelzip->value.' '.$this->oxorder__oxdelcity->getRawValue() );
            $oPdf->setFont( $oPdfBlock->getFont(), '', 10 );
            $oPdf->text( 15, 79, $this->oxorder__oxdelcountry->getRawValue() );
        } else {
            // no delivery address - billing address is used for delivery
            $this->_setBillingAddressToPdf( $oPdf );
        }

        // loading user info
        $oUser = oxNew( 'oxuser' );
        $oUser->load( $this->oxorder__oxuserid->value );

        // user info
        $sText = $this->translate( 'ORDER_OVERVIEW_PDF_FILLONPAYMENT' );
        $oPdf->setFont( $oPdfBlock->getFont(), '', 5 );
        $oPdf->text( 195 - $oPdf->getStringWidth( $sText ), 70, $sText );

        // customer number
        $sCustNr = $this->translate( 'ORDER_OVERVIEW_PDF_CUSTNR' ).' '.$oUser->oxuser__oxcustnr->value;
        $oPdf->setFont( $oPdfBlock->getFont(), '', 7 );
        $oPdf->text( 195 - $oPdf->getStringWidth( $sCustNr ), 73, $sCustNr );

        // shops city
        $sText = $oShop->oxshops__oxcity->getRawValue().', '.date( 'd.m.Y' );
        $oPdf->setFont( $oPdfBlock->getFont(), '', 10 );
        $oPdf->text( 195 - $oPdf->getStringWidth( $sText ), 95, $sText );

        $iTop = 99;
        // shop VAT number
        if ( $oShop->oxshops__oxvatnumber->value ) {
            $sText = $this->translate( 'ORDER_OVERVIEW_PDF_TAXIDNR' ).' '.$oShop->oxshops__oxvatnumber->value;
            $oPdf->text( 195 - $oPdf->getStringWidth( $sText ), $iTop, $sText );
            $iTop += 4;
        }

        // invoice number
        $sText = $this->translate( 'ORDER_OVERVIEW_PDF_COUNTNR' ).' '.$this->oxorder__oxbillnr->value;
        $oPdf->text( 195 - $oPdf->getStringWidth( $sText ), $iTop, $sText );

        // canceled order marker
        if ( $this->oxorder__oxstorno->value == 1 ) {
            $this->oxorder__oxordernr->setValue( $this->oxorder__oxordernr->getRawValue() . '   '.$this->translate( 'ORDER_OVERVIEW_PDF_STORNO' ), oxField::T_RAW );
        }

        // order number
        $oPdf->setFont( $oPdfBlock->getFont(), '', 12 );
        $oPdf->text( 15, 108, $this->translate( 'ORDER_OVERVIEW_PDF_DELIVNOTE' ).' '.$this->oxorder__oxordernr->value );

        // order date
        $aOrderDate = explode( ' ', $this->oxorder__oxorderdate->value );
        $sOrderDate = oxUtilsDate::getInstance()->formatDBDate( $aOrderDate[0]);
        $oPdf->setFont( $oPdfBlock->getFont(), '', 10 );
        $oPdf->text(15, 119, $this->translate( 'ORDER_OVERVIEW_PDF_ORDERSFROM' ).$sOrderDate.$this->translate( 'ORDER_OVERVIEW_PDF_ORDERSAT' ).$oShop->oxshops__oxurl->value );

        // product info header
        $oPdf->setFont( $oPdfBlock->getFont(), '', 8 );
        $oPdf->text( 15, 128, $this->translate( 'ORDER_OVERVIEW_PDF_AMOUNT' ) );
        $oPdf->text( 30, 128, $this->translate( 'ORDER_OVERVIEW_PDF_ARTID' ) );
        $oPdf->text( 45, 128, $this->translate( 'ORDER_OVERVIEW_PDF_DESC' ) );

        // line separator
        $oPdf->line( 15, 130, 195, 130 );

        // product list
        $oPdf->setFont( $oPdfBlock->getFont(), '', 10 );
        $siteH = 130;

        // order articles
        $this->_setOrderArticlesToPdf( $oPdf, $siteH, false );

        // sine separator
        $oPdf->line( 15, $siteH + 2, 195, $siteH + 2 );
        $siteH += 4;

        // payment date
        $oPdf->setFont( $oPdfBlock->getFont(), '', 10 );
        $text = $this->translate( 'ORDER_OVERVIEW_PDF_PAYUPTO' ).date( 'd.m.Y', mktime( 0, 0, 0, date ( 'm' ), date ( 'd' ) + 7, date( 'Y' ) ) );
        $oPdf->text( 15, $siteH + 4, $text );
    }

    /**
     * Replaces some special characters to HTML compatible symbol codes.
     * SWITCHED OFF NOW ( 2.2 )
     *
     * @param string $sValue    initial value
     * @param bool   $blReverse (default false) if false - checks if we do have already htmlentities inside
     *
     * @return string
     */
    protected function _replaceExtendedChars( $sValue, $blReverse = false )
    {
        // we need to replace this for compatibility with XHTML
        // as this function causes a lot of trouble with editor
        // we switch it off, even if this means that fields do not validate through xhtml
        // return $sValue;

        // we need to replace this for compatibility with XHTML
        $aReplace = array( chr(169) => "&copy;", chr(128) => "&euro;", "\"" => "&quot;", "'" => "&#039;");

        // #899C reverse html entities and references transformation is used in invoicepdf module
        // so this part must be enabled. Now it works with html references like &#123;
        if ($blReverse) {
            // replace now
            if (version_compare(PHP_VERSION, '5.3.4') >= 0) {
                $aTransTbl = get_html_translation_table (HTML_ENTITIES, ENT_COMPAT, 'ISO-8859-1');
            } else {
                $aTransTbl = get_html_translation_table (HTML_ENTITIES, ENT_COMPAT);
            }

            $aTransTbl = array_flip ($aTransTbl) + array_flip ($aReplace);
            $sValue = strtr($sValue, $aTransTbl);
            $sValue = getStr()->preg_replace('/\&\#([0-9]+)\;/me', "chr('\\1')", $sValue);
        }

        return $sValue;
    }

    /**
     * Returns order articles VATS's
     *
     * @return array
     */
    public function getVats()
    {
        // for new orders
        $aVats = $this->getProductVats( false );

        // for older orders
        return ( is_array( $aVats ) && count( $aVats ) ) ? $aVats : $this->_aVATs;
    }

    /**
     * Returns order currency object
     *
     * @return object
     */
    public function getCurrency()
    {
        return $this->_oCur;
    }

    /**
     * Returns order currency object
     *
     * @return object
     */
    public function getSelectedLang()
    {
        return $this->_iSelectedLang;
    }
}
