<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
require "_header.php"; ?>
<b><?php $this->getText('STEP_5_DESC'); ?></b><br>
<br>
<form action="index.php" method="post">
<input type="hidden" name="istep" value="<?php $this->getSetupStep('STEP_SERIAL_SAVE'); ?>">

<table cellpadding="0" cellspacing="5" border="0">
  <tr>
    <td><?php $this->getText('STEP_5_LICENCE_KEY'); ?>:</td>
    <td>&nbsp;&nbsp;<input size="47" name="sLicence" class="editinput" value="<?php echo $this->getViewParam("sLicense"); ?>"></td>
    <td>&nbsp;&nbsp;<input type="submit" id="step5Submit" class="edittext" value="<?php $this->getText('BUTTON_WRITE_LICENCE'); ?>"></td>
  </tr>
</table>
<br>
<input type="hidden" name="sid" value="<?php $this->getSid(); ?>">
</form>
<?php
$this->getText('STEP_5_LICENCE_DESC');
require "_footer.php";
