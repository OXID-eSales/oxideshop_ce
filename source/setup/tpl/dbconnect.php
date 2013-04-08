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
 * @package   setup
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id: lang.php 25584 2010-02-03 12:11:40Z arvydas $
 */
require "_header.php"; ?>
<b><?php $this->getText('STEP_3_1_DB_CONNECT_IS_OK'); ?></b><br>
<?php
if ( $this->getViewParam( "blCreated" ) === 1 ) {
    $aDB = $this->getViewParam( "aDB" );
    ?><b><?php printf( $this->getText('STEP_3_1_DB_CREATE_IS_OK', false ), $aDB['dbName'] ); ?></b><br><?php
}
?>
<br><?php $this->getText('STEP_3_1_CREATING_TABLES'); ?><br>
<?php require "_footer.php";