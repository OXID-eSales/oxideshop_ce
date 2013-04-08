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
<br><br>
<?php
$this->getText('STEP_4_DESC');
$aPath = $this->getViewParam( "aPath" );
$aSetupConfig = $this->getViewParam( "aSetupConfig" );
$aAdminData   = $this->getViewParam( "aAdminData" );
$sChecked = "";
if ( isset( $aSetupConfig['blDelSetupDir'] ) ) {
    if ( $aSetupConfig['blDelSetupDir']) {
        $sChecked = "checked";
    }
} else {
    $sChecked = "checked";
}
?><br>
<br>
<form action="index.php" method="post">
<input type="hidden" name="istep" value="<?php $this->getSetupStep('STEP_DIRS_WRITE'); ?>">
<input type="hidden" name="aSetupConfig[blDelSetupDir]" type="checkbox" value="1">

<table cellpadding="0" cellspacing="5" border="0">
  <tr>
    <td><?php $this->getText('STEP_4_SHOP_URL'); ?>:</td>
    <td>&nbsp;&nbsp;<input size="40" name="aPath[sShopURL]" class="editinput" value="<?php echo( $aPath['sShopURL']);?>"> </td>
  </tr>
  <tr>
    <td><?php $this->getText('STEP_4_SHOP_DIR'); ?>:</td>
    <td>&nbsp;&nbsp;<input size="40" name="aPath[sShopDir]" class="editinput" value="<?php echo( $aPath['sShopDir']);?>"> </td>
  </tr>
  <tr>
    <td><?php $this->getText('STEP_4_SHOP_TMP_DIR'); ?>:</td>
    <td>&nbsp;&nbsp;<input size="40" name="aPath[sCompileDir]" class="editinput" value="<?php echo( $aPath['sCompileDir']);?>"> </td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td><?php $this->getText('STEP_4_ADMIN_LOGIN_NAME'); ?>:</td>
    <td>&nbsp;&nbsp;<input size="40" name="aAdminData[sLoginName]" class="editinput" value="<?php echo( $aAdminData['sLoginName']);?>"> </td>
  </tr>
  <tr>
    <td><?php $this->getText('STEP_4_ADMIN_PASS'); ?>:</td>
    <td>&nbsp;&nbsp;<input size="40" name="aAdminData[sPassword]" class="editinput" type="password"> <?php $this->getText('STEP_4_ADMIN_PASS_MINCHARS'); ?></td>
  </tr>
  <tr>
    <td><?php $this->getText('STEP_4_ADMIN_PASS_CONFIRM'); ?>:</td>
    <td>&nbsp;&nbsp;<input size="40" name="aAdminData[sPasswordConfirm]" class="editinput" type="password"> </td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
</table>
<input type="hidden" name="sid" value="<?php $this->getSid(); ?>">
<input type="submit" id="step4Submit" class="edittext" value="<?php $this->getText('BUTTON_WRITE_DATA'); ?>">
</form>
<?php require "_footer.php";