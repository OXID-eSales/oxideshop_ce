<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
require "_header.php"; ?>
<br><br>
<?php
$this->getText('STEP_4_DESC');
$aPath = $this->getViewParam("aPath");
$aSetupConfig = $this->getViewParam("aSetupConfig");
$aAdminData   = $this->getViewParam("aAdminData");
$sChecked = "";
if (!isset($aSetupConfig['blDelSetupDir']) || $aSetupConfig['blDelSetupDir']) {
    $sChecked = "1";
} else {
    $sChecked = "0";
}
?><br>
<br>
<form action="index.php" method="post">
<input type="hidden" name="istep" value="<?php $this->getSetupStep('STEP_DIRS_WRITE'); ?>">
<input type="hidden" name="aSetupConfig[blDelSetupDir]" type="checkbox" value="<?php echo($sChecked) ?>">

<table cellpadding="0" cellspacing="5" border="0">
  <tr>
    <td><?php $this->getText('STEP_4_SHOP_URL'); ?>:</td>
    <td>&nbsp;&nbsp;<input size="40" name="aPath[sShopURL]" class="editinput" value="<?php echo($aPath['sShopURL']);?>"> </td>
  </tr>
  <tr>
    <td><?php $this->getText('STEP_4_SHOP_DIR'); ?>:</td>
    <td>&nbsp;&nbsp;<input size="40" name="aPath[sShopDir]" class="editinput" value="<?php echo($aPath['sShopDir']);?>"> </td>
  </tr>
  <tr>
    <td><?php $this->getText('STEP_4_SHOP_TMP_DIR'); ?>:</td>
    <td>&nbsp;&nbsp;<input size="40" name="aPath[sCompileDir]" class="editinput" value="<?php echo($aPath['sCompileDir']);?>"> </td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td><?php $this->getText('STEP_4_ADMIN_LOGIN_NAME'); ?>:</td>
    <td>&nbsp;&nbsp;<input size="40" name="aAdminData[sLoginName]" class="editinput" value="<?php echo($aAdminData['sLoginName']);?>"> </td>
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
