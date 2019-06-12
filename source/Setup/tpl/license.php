<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
require "_header.php"; ?>
<textarea readonly="readonly" cols="180" rows="20" class="edittext" style="width: 98%; padding: 7px;"><?php echo $this->getViewParam("aLicenseText"); ?></textarea>
<form action="index.php" method="post">
  <input type="hidden" name="istep" value="<?php $this->getSetupStep('STEP_DB_INFO'); ?>">
  <input type="radio" name="iEula" value="1"><?php $this->getText('BUTTON_RADIO_LICENCE_ACCEPT'); ?><br>
  <input type="radio" name="iEula" value="0" checked><?php $this->getText('BUTTON_RADIO_LICENCE_NOT_ACCEPT'); ?><br><br>
  <input type="hidden" name="sid" value="<?php $this->getSid(); ?>">
  <input type="submit" id="step2Submit" class="edittext" value="<?php $this->getText('BUTTON_LICENCE'); ?>">
</form>
<?php require "_footer.php";
