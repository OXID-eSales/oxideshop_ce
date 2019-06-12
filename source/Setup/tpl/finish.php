<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
require "_header.php";

// caching output
ob_flush();
require "_footer.php";
$sFooter = ob_get_contents();
ob_clean();

$this->getText('STEP_6_DESC');
$aPath = $this->getViewParam("aPath");
$aSetupConfig = $this->getViewParam("aSetupConfig");
$aDB = $this->getViewParam("aDB");
$blWritableConfig  = $this->getViewParam("blWritableConfig");
// This must be done here as it deletes setup and nothing can't be displayed after that.
$blRemoved = $this->isDeletedSetup($aSetupConfig, $aDB);
?>
<br><br>
<table cellspacing="5" cellpadding="5">
  <tr>
    <td><?php $this->getText('STEP_6_LINK_TO_SHOP'); ?>: </td>
    <td><a href="<?php echo($aPath['sShopURL']); ?>/" target="_blank" id="linkToShop" style="text-decoration: underline"><strong><?php $this->getText('STEP_6_TO_SHOP'); ?></strong></a></td>
  </tr>
  <tr>
    <td><?php $this->getText('STEP_6_LINK_TO_SHOP_ADMIN_AREA'); ?>: </td>
    <td><a href="<?php echo($aPath['sShopURL']); ?>/admin/" target="_blank" id="linkToAdmin" style="text-decoration: underline"><strong><?php $this->getText('STEP_6_TO_SHOP_ADMIN'); ?></strong></a></td>
  </tr>
</table>
<br>
<?php
//finalizing installation
if (!$blRemoved || $blWritableConfig) {
    ?><strong class="attention-title"><?php $this->getText('ATTENTION'); ?>:</strong><br><br><?php
}
if (!$blRemoved) {
    ?><strong class="attention-item"><?php $this->getText('SETUP_DIR_DELETE_NOTICE'); ?></strong><br><br><?php
}

if ($blWritableConfig) {
    ?><strong class="attention-item"><?php $this->getText('SETUP_CONFIG_PERMISSIONS'); ?></strong><br><?php
}
ob_flush();
echo $sFooter;
