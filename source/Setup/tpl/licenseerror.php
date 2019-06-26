<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
require "_header.php"; ?>
</br></br>
<form action="index.php" method="post">
  <input type="hidden" name="sid" value="<?php $this->getSid(); ?>">
  <input type="hidden" name="istep" value="<?php $this->getText('STEP_WELCOME'); ?>">
  <input type="submit" id="step0Submit" class="edittext" value="<?php $this->getText('BUTTON_START_INSTALL'); ?>">
</form>
<?php require "_footer.php";
