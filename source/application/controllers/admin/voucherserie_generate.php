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
 * Voucher Serie generator class
 *
 * @package admin
 */
class VoucherSerie_Generate extends VoucherSerie_Main
{
    /**
     * Voucher generator class name
     *
     * @var string
     */
    public $sClassDo = "voucherserie_generate";

    /**
     * Number of vouchers to generate per tick
     *
     * @var int
     */
    public $iGeneratePerTick = 100;

    /**
     * Current class template name
     *
     * @var string
     */
    protected $_sThisTemplate = "voucherserie_generate.tpl";

    /**
     * Voucher serie object
     *
     * @var oxvoucherserie
     */
    protected $_oVoucherSerie = null;

    /**
     * Generated vouchers count
     *
     * @var int
     */
    protected $_iGenerated = false;

    /**
     * Generates vouchers by offset iCnt
     *
     * @param integer $iCnt voucher offset
     *
     * @return bool
     */
    public function nextTick( $iCnt )
    {
        if ( $iGeneratedItems = $this->generateVoucher( $iCnt ) ) {
            return $iGeneratedItems;
        }

        return false;
    }

    /**
     * Generates and saves vouchers. Returns number of saved records
     *
     * @param int $iCnt voucher counter offset
     *
     * @return int saved record count
     */
    public function generateVoucher( $iCnt )
    {
        $iAmount = abs( (int) oxSession::getVar( "voucherAmount" ) );

        // creating new vouchers
        if ( $iCnt < $iAmount && ( $oVoucherSerie = $this->_getVoucherSerie() ) ) {

            if ( !$this->_iGenerated ) {
                $this->_iGenerated = $iCnt;
            }

            $blRandomNr = ( bool ) oxSession::getVar( "randomVoucherNr" );
            $sVoucherNr = $blRandomNr ? oxUtilsObject::getInstance()->generateUID() : oxSession::getVar( "voucherNr" );

            $oNewVoucher = oxNew( "oxvoucher" );
            $oNewVoucher->oxvouchers__oxvoucherserieid = new oxField( $oVoucherSerie->getId() );
            $oNewVoucher->oxvouchers__oxvouchernr = new oxField( $sVoucherNr );
            $oNewVoucher->save();

            $this->_iGenerated++;
        }

        return $this->_iGenerated;
    }

    /**
     * Runs voucher generation
     *
     * @return null
     */
    public function run()
    {
        $blContinue = true;
        $iExportedItems = 0;

        // file is open
        $iStart = oxConfig::getParameter("iStart");

        for ( $i = $iStart; $i < $iStart + $this->iGeneratePerTick; $i++) {
            if ( ( $iExportedItems = $this->nextTick( $i ) ) === false ) {
                // end reached
                $this->stop( ERR_SUCCESS );
                $blContinue = false;
                break;
            }
        }

        if ( $blContinue) {
            // make ticker continue
            $this->_aViewData['refresh'] = 0;
            $this->_aViewData['iStart']  = $i;
            $this->_aViewData['iExpItems'] = $iExportedItems;
        }
    }
}
