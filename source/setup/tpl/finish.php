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
require "_header.php";

// caching output
ob_flush();
require "_footer.php";
$sFooter = ob_get_contents();
ob_clean();

$this->getText('STEP_6_DESC');
$aPath = $this->getViewParam( "aPath" );
$aSetupConfig = $this->getViewParam( "aSetupConfig" );
$blWritableConfig  = $this->getViewParam( "blWritableConfig" );
//$blRemoved = ( isset( $aSetupConfig['blDelSetupDir'] ) && $aSetupConfig['blDelSetupDir'] ) ? $this->isDeletedSetup() : true;
$blRemoved = $this->isDeletedSetup();
?>
<br><br>
<table cellspacing="5" cellpadding="5">
  <tr>
    <td><?php $this->getText('STEP_6_LINK_TO_SHOP'); ?>: </td>
    <td><a href="<?php echo( $aPath['sShopURL']); ?>/" target="_blank" id="linkToShop" style="text-decoration: underline"><strong><?php $this->getText('STEP_6_TO_SHOP'); ?></strong></a></td>
  </tr>
  <tr>
    <td><?php $this->getText('STEP_6_LINK_TO_SHOP_ADMIN_AREA'); ?>: </td>
    <td><a href="<?php echo( $aPath['sShopURL']); ?>/admin/" target="_blank" id="linkToAdmin" style="text-decoration: underline"><strong><?php $this->getText('STEP_6_TO_SHOP_ADMIN'); ?></strong></a></td>
  </tr>
</table>
<br>
<?php
//finalizing installation
if ( !$blRemoved || $blWritableConfig ) {
    ?><strong style="font-size:16px;color:red;"><?php $this->getText('ATTENTION'); ?>:</strong><br><br><?php
}
if ( !$blRemoved ) {
    ?><strong style="font-size:16px;color:red;"><?php $this->getText('SETUP_DIR_DELETE_NOTICE'); ?></strong><br><br><?php
}

if ( $blWritableConfig ) {
    ?><strong style="font-size:16px;color:red;"><?php $this->getText('SETUP_CONFIG_PERMISSIONS'); ?></strong><br><?php
}
ob_flush();
echo $sFooter;