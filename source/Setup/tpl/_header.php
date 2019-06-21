<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

$facts = new \OxidEsales\Facts\Facts();

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title><?php $this->getText('HEADER_META_MAIN_TITLE'); ?> - <?php echo($this->getTitle()) ?></title>
    <meta name="ROBOTS" content="NOINDEX, NOFOLLOW">
    <script language="JavaScript">
    <!--
    function showPopUp( url, w, h, r )
    {
        if (url !== null && url.length > 0) {
            var iLeft = (window.screen.width - w)/2;
            var iTop = (window.screen.height - h)/2;
            var _cfg = "status=yes,scrollbars=no,menubar=no,top="+iTop+",left="+iLeft+",width="+w+",height="+h+(r?",resizable=yes":"");
            window.open(url, "_blank", _cfg);
        }
    }

    /**
     * Replaces password type field into plain and vice versa
     */
    function changeField()
    {
        var oField = document.getElementsByName( "aDB[dbPwd]" );
        doChange( oField[0], oField[1] );
        doChange( oField[1], oField[0] )
    }
    function doChange( oField1, oField2 )
    {
        if ( oField1.disabled ) {
            oField1.disabled = '';
            oField1.style.display = '';
            oField1.value = oField2.value;
        } else {
            oField1.disabled = 'disabled';
            oField1.style.display = 'none';
            oField2.value = oField1.value;
        }
    }

    -->
    </script>
    <style type="text/css">
        <?php
            $cssPath = str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/../out/src/');
            $imgPath = str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/../out/src/img/');
            $imgLogo = 'data:image/svg+xml;base64,' . base64_encode(file_get_contents($imgPath . 'logo_dark.svg'));
            $cssData = file_get_contents($cssPath . 'main.css');
            preg_match_all("/url\('img\/([^']+)'\);/", $cssData, $matches);
            $images = array_unique($matches[1]);
        foreach ($images as $image) {
            $imgData = 'data:image/png;base64,' . base64_encode(file_get_contents($imgPath . $image));
            $cssData = str_replace('img/' . $image, $imgData, $cssData);
        }
            echo $cssData;
        ?>
    </style>
    <style type="text/css">
        <?php
            $iTabWidth = 147;
            $iSepWidth = 3;
        if ($facts->isEnterprise()) {
            $iTabCount = 7;
        }
        if ($facts->isProfessional()) {
            $iTabCount = 7;
        }
        if ($facts->isCommunity()) {
            $iTabCount = 6;
        }
            $iDocWidth = ($iTabWidth + $iSepWidth)*$iTabCount;
        ?>
        #page { width: <?php echo $iDocWidth; ?>px; }
        dl.tab { width: <?php echo $iTabWidth; ?>px; }
    </style>

    <?php
    if (($iRedir2Step = $this->getNextSetupStep()) !== null) {
        ?><meta http-equiv="refresh" content="3; URL=index.php?istep=<?php echo $iRedir2Step; ?>&sid=<?php $this->getSid(); ?>"><?php
    }
    ?>
</head>

<body>
<div id="page">
    <a href="index.php?istep=<?php $this->getSetupStep('STEP_SYSTEMREQ'); ?>&sid=<?php $this->getSid(); ?>"><img src="<?php echo $imgLogo; ?>" class="oxid_eshop_logo" alt="OXID eSales"></a>
    <div id="header">
        <?php
        $iCntr = 0;
        foreach ($this->getSetupSteps() as $iTab) :
            // only "real" steps
            if (fmod($iTab, 100)) {
                continue;
            }

            $blAct = (floor($this->getCurrentSetupStep() / 100) == ($iTab / 100));
            $iStepId = floor($iTab / 100) - 1;
            $iCntr++;

            $sTabClass = $sTabLinkOpen = $sTabLinkClose = '';
            if ($blAct) {
                $sTabClass     = 'act';
                $sTabLinkOpen  = '<a href="index.php?istep='.$iTab.'&sid='.$this->getSid(false).'">';
                $sTabLinkClose = '</a>';
            }
            ?>
            <dl class="tab <?php echo $sTabClass; ?>">
                <dt><?php echo $sTabLinkOpen ?><?php echo $iCntr ,'. ',$this->getText('TAB_'.$iStepId.'_TITLE', false); ?><?php echo $sTabLinkClose?></dt>
                <dd><?php echo $sTabLinkOpen ?><?php $this->getText('TAB_'.$iStepId.'_DESC'); ?><?php echo $sTabLinkClose?></dd>
            </dl>
            <?php
        endforeach;
        ?>
    </div>

    <div id="body">
    <?php
    $aMessages = $this->getMessages();
    foreach ($this->getMessages() as $sMessage) {
        ?><br><b><?php echo $sMessage; ?></b><?php
    }
    if (count($aMessages)) {
        ?><br><br><?php
    }

    if (($iRedir2Step = $this->getNextSetupStep()) !== null) {
        ?><br><br><?php $this->getText('HEADER_TEXT_SETUP_NOT_RUNS_AUTOMATICLY'); ?>
        <a href="index.php?istep=<?php echo $iRedir2Step; ?>&sid=<?php $this->getSid(); ?>" id="continue"><b><?php $this->getText('HERE'); ?></b></a>.<br><br><?php
    }
