<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
require "_header.php"; ?>
<?php
$this->getText('STEP_3_DESC');
$aDB = $this->getViewParam("aDB");
$demodataPackageExists = $this->getViewParam('demodataPackageExists');

?><br>
<br>
<form action="index.php" method="post">
<input type="hidden" name="istep" value="<?php $this->getSetupStep('STEP_DB_CONNECT'); ?>">

<table cellpadding="0" cellspacing="5" border="0">
  <tr>
    <td><?php $this->getText('STEP_3_DB_HOSTNAME'); ?>:</td>
    <td>&nbsp;&nbsp;<input size="40" name="aDB[dbHost]" class="editinput" value="<?php echo($aDB['dbHost']);?>"> </td>
  </tr>
  <tr>
    <td><?php $this->getText('STEP_3_DB_PORT'); ?>:</td>
    <td>&nbsp;&nbsp;<input size="40" name="aDB[dbPort]" class="editinput" value="<?php echo($aDB['dbPort']);?>"> </td>
  </tr>
  <tr>
    <td><?php $this->getText('STEP_3_DB_DATABSE_NAME'); ?>:</td>
    <td>&nbsp;&nbsp;<input size="40" name="aDB[dbName]" class="editinput" value="<?php echo($aDB['dbName']);?>"><br>&nbsp;&nbsp;(<?php $this->getText('STEP_3_CREATE_DB_WHEN_NO_DB_FOUND'); ?>)</td>
  </tr>
  <tr>
    <td><?php $this->getText('STEP_3_DB_USER_NAME'); ?>:</td>
    <td>&nbsp;&nbsp;<input size="40" name="aDB[dbUser]" class="editinput" value="<?php echo($aDB['dbUser']);?>"> </td>
  </tr>
  <tr>
    <td><?php $this->getText('STEP_3_DB_PASSWORD'); ?>:</td>
    <td>
        &nbsp;&nbsp;<input size="40" name="aDB[dbPwd]" id="sDbPass" class="editinput" type="password" value="<?php echo($aDB['dbPwd']);?>"><input size="40" name="aDB[dbPwd]" id="sDbPassPlain" class="editinput" type="text" disabled="disabled" style="display:none">
        <input type="checkbox" id="sDbPassCheckbox" onClick="JavaScript:changeField();"><?php $this->getText('STEP_3_DB_PASSWORD_SHOW'); ?>
    </td>
  </tr>
  <tr>
    <td><?php $this->getText('STEP_3_DB_DEMODATA'); ?>:</td>
    <td>
        &nbsp;&nbsp;<input type="radio" name="aDB[dbiDemoData]" value="1" <?php if ($aDB['dbiDemoData'] == 1) {
            echo("checked");
                                                                          } ?> <?php echo !$demodataPackageExists ? "disabled" : "" ?>><?php $this->getText('BUTTON_RADIO_INSTALL_DB_DEMO'); ?> <?php echo !$demodataPackageExists ? "<span class='exclamation-icon'></span>" : "" ?><br>
        &nbsp;&nbsp;<input type="radio" name="aDB[dbiDemoData]" value="0" <?php if ($aDB['dbiDemoData'] == 0) {
            echo("checked");
                                                                          } ?>><?php $this->getText('BUTTON_RADIO_NOT_INSTALL_DB_DEMO'); ?><br>
    </td>
  </tr>
</table>
<input type="hidden" name="sid" value="<?php $this->getSid(); ?>">

<?php if (!$demodataPackageExists) { ?>
    <ul class="req"><li class="pmin"><?php $this->getText('NOTICE_NO_DEMODATA_INSTALLED'); ?></li></ul><br>
<?php } ?>

<input type="submit" id="step3Submit" class="edittext" value="<?php $this->getText('BUTTON_DB_CREATE'); ?>">
</form>
<?php require "_footer.php";
