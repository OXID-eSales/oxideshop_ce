<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
require "_header.php"; ?>
<strong><?php $this->getText('STEP_0_DESC'); ?></strong><br><br>

<table cellpadding="1" cellspacing="0">
    <tr>
        <td nowrap><?php $this->getText('SELECT_SETUP_LANG'); ?>: </td>
        <td>
            <form action="index.php" id="langSelectionForm" method="post">
            <select name="setup_lang" onChange="document.getElementById('langSelectionForm').submit();" style="font-size: 11px;">
            <?php
            $aLanguages = $this->getViewParam("aLanguages");
            foreach ($aLanguages as $sLangId => $sLangTitle) {
                ?>
                <option value="<?php echo $sLangId; ?>" <?php if ($this->getViewParam("sLanguage") == $sLangId) {
                    echo 'selected';
                               } ?>><?php echo $sLangTitle; ?></option>
                <?php
            }
            ?>
            </select>
            <noscript>
            <input type="submit" name="setup_lang_submit" value="<?php $this->getText('SELECT_SETUP_LANG_SUBMIT'); ?>" style="font-size: 11px;">
            </noscript>
            <input type="hidden" name="sid" value="<?php $this->getSid(); ?>">
            <input type="hidden" name="istep" value="<?php $this->getSetupStep('STEP_SYSTEMREQ'); ?>">
            </form>
        </td>
        <td style="padding: 0px 5px;"><?php $this->getText('SELECT_SETUP_LANG_HINT'); ?></td>
    </tr>
</table>
<br>

    <ul class="req">
    <?php
    $aGroupModuleInfo = $this->getViewParam("aGroupModuleInfo");
    foreach ($aGroupModuleInfo as $sGroupName => $aGroupInfo) {
        ?><li class="group"><?php echo $sGroupName; ?><ul><?php
foreach ($aGroupInfo as $aModuleInfo) {
    ?><li id="<?php echo $aModuleInfo['module']; ?>" class="<?php echo $aModuleInfo['class']; ?>"><?php
if ($aModuleInfo['class'] == "fail" || $aModuleInfo['class'] == "pmin" || $aModuleInfo['class'] == "null") {
    ?><a href="<?php $this->getReqInfoUrl($aModuleInfo['module']); ?>" target="_blank"><?php
}
    echo $aModuleInfo['modulename'];
if ($aModuleInfo['class'] == "fail" || $aModuleInfo['class'] == "pmin" || $aModuleInfo['class'] == "null") {
    ?></a><?php
} ?></li><?php
} ?></ul></li><?php
    }
    ?><li class="clear"></li></ul>
    <?php $this->getText('STEP_0_TEXT'); ?>
    <br><br>

<?php if ($this->getViewParam("blContinue") === true) { ?>
<form action="index.php" method="post">
<input type="hidden" name="sid" value="<?php $this->getSid(); ?>">
<input type="hidden" name="istep" value="<?php $this->getSetupStep('STEP_WELCOME'); ?>">
<input type="submit" id="step0Submit" class="edittext" value="<?php $this->getText('BUTTON_PROCEED_INSTALL'); ?>">
</form>
    <?php
} else {
    ?><b><?php $this->getText('STEP_0_ERROR_TEXT'); ?></b><br>
    <a target="_blank" href="<?php $this->getText('STEP_0_ERROR_URL'); ?>"><?php $this->getText('STEP_0_ERROR_URL'); ?></a><?php
}
require "_footer.php";
