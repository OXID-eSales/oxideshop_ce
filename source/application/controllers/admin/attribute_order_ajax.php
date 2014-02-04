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
 * Class manages article select lists sorting
 */
class attribute_order_ajax extends ajaxListComponent
{
    /**
     * Columns array
     *
     * @var array
     */
    protected $_aColumns = array( 'container1' => array(
                                        array( 'oxtitle', 'oxattribute', 1, 1, 0 ),
                                        array( 'oxsort',  'oxcategory2attribute', 1, 0, 0 ),
                                        array( 'oxid',    'oxcategory2attribute', 0, 0, 1 )
                                        )
                                    );

    /**
     * Returns SQL query for data to fetc
     *
     * @return string
     */
    protected function _getQuery()
    {
        $sSelTable = $this->_getViewName('oxattribute');
        $sArtId    = oxConfig::getParameter( 'oxid' );

        $sQAdd = " from $sSelTable left join oxcategory2attribute on oxcategory2attribute.oxattrid = $sSelTable.oxid where oxobjectid = " . oxDb::getDb()->quote( $sArtId ) . " ";

        return $sQAdd;
    }

    /**
     * Returns SQL query addon for sorting
     *
     * @return string
     */
    protected function _getSorting()
    {
        return 'order by oxcategory2attribute.oxsort ';
    }

    /**
     * Applies sorting for selection lists
     *
     * @return null
     */
    public function setSorting()
    {
        $sSelId  = oxConfig::getParameter( 'oxid' );
        $sSelect = "select * from oxcategory2attribute where oxobjectid= " . oxDb::getDb()->quote( $sSelId ) . " order by oxsort";

        $oList = oxNew( "oxlist" );
        $oList->init( "oxbase", "oxcategory2attribute" );
        $oList->selectString( $sSelect );

        // fixing indexes
        $iSelCnt = 0;
        $aIdx2Id = array();
        foreach ( $oList as $sKey => $oSel ) {
            if ( $oSel->oxcategory2attribute__oxsort->value != $iSelCnt ) {
                $oSel->oxcategory2attribute__oxsort->setValue($iSelCnt);
                // saving new index
                $oSel->save();
            }
            $aIdx2Id[$iSelCnt] = $sKey;
            $iSelCnt++;
        }
        //
        if ( ( $iKey = array_search( oxConfig::getParameter( 'sortoxid' ), $aIdx2Id ) ) !== false ) {
            $iDir = (oxConfig::getParameter( 'direction' ) == 'up')?($iKey-1):($iKey+1);
            if ( isset( $aIdx2Id[$iDir] ) ) {
                // exchanging indexes
                $oDir1 = $oList->offsetGet( $aIdx2Id[$iDir] );
                $oDir2 = $oList->offsetGet( $aIdx2Id[$iKey] );

                $iCopy = $oDir1->oxcategory2attribute__oxsort->value;
                $oDir1->oxcategory2attribute__oxsort->setValue($oDir2->oxcategory2attribute__oxsort->value);
                $oDir2->oxcategory2attribute__oxsort->setValue($iCopy);
                $oDir1->save();
                $oDir2->save();
            }
        }

        $sQAdd = $this->_getQuery();

        $sQ      = 'select ' . $this->_getQueryCols() . $sQAdd;
        $sCountQ = 'select count( * ) ' . $sQAdd;

        $this->_outputResponse( $this->_getData( $sCountQ, $sQ ) );
    }
}
