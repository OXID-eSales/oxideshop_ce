<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

$editionSelector = new \OxidEsales\EshopCommunity\Core\Edition\EditionSelector();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title><?php $this->getText( 'HEADER_META_MAIN_TITLE'); ?> - <?php echo( $this->getTitle() ) ?></title>
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

<?php if (!$editionSelector->isEnterprise()) { ?>
    function update_dynpages_checkbox()
    {
        sValue = document.forms[0].location_lang.value;
        if ( sValue == '' ) {
            document.getElementById('use_dynamic_pages_ckbox').style.display = 'none';
            document.getElementById('use_dynamic_pages_desc').style.display = 'none';
        } else {
            document.getElementById('use_dynamic_pages_ckbox').style.display = '';
            document.getElementById('use_dynamic_pages_desc').style.display = '';
        }
    }
<?php } ?>
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
        $iTabWidth = 147;
        $iSepWidth = 3;
        if ($editionSelector->isEnterprise()) {
            $iTabCount = 7;
            $sHColor = '#006ab4';
        }
        if ($editionSelector->isProfessional()) {
            $iTabCount = 7;
            $sHColor = '#cd0210';
        }
        if ($editionSelector->isCommunity()) {
            $iTabCount = 6;
            $sHColor = '#ff3600';
        }
        $iDocWidth = ($iTabWidth + $iSepWidth)*$iTabCount;
    ?>
        body, p , form {margin:0; }
        body, p, td, tr, ol, ul, input, textarea {font:11px/130% Trebuchet MS, Tahoma, Verdana, Arial, Helvetica, sans-serif;}

        a {text-decoration: none;color: #000;}
        a:hover {text-decoration: underline;}

        #page {width:<?php echo $iDocWidth; ?>px;margin:5% auto;}
        #header {clear:both;margin-top:10px;}
        #body   {clear:both;padding:20px 10px;background: #e4e4e4 url(setup.png) 0 -80px repeat-x;border:1px solid #ccc;border-top:none;margin:-10px 1px 0 0;min-height: 350px;}
        #footer {clear:both;background:#888;color:#fff;padding:5px 10px;margin-right:1px;}

        dl.tab {float:left;width: <?php echo $iTabWidth; ?>px;height:80px;margin:0;margin-right:1px;background:#ccc url(setup.png);border:1px solid #ccc;border-bottom:none;margin-bottom:-1px;}
        dl.tab dt{display:block;padding:0;margin:0;padding:10px 5px 0 5px;font-weight: bold;}
        dl.tab a{color:#888;}
        dl.tab dd{display:block;padding:0;margin:0;padding:5px;height: 50px;}

        dl.tab.act {border-color:<?php echo $sHColor; ?>;}
        dl.tab.act dt a{color: <?php echo $sHColor; ?>;}
        dl.tab.act dd{}
        dl.tab.act dd a{color: #000;}

        ul.req {padding:0 5px;border:1px solid #888;margin:5px 0;clear:both;display:block;}
        ul.req li{list-style:none;margin:5px 0;padding-left:1.5em;}
        ul.req li.pass{background-image:url('./out/src/img/pass.png');background-repeat: no-repeat;}
        ul.req li.pmin{background-image:url('./out/src/img/pmin.png');background-repeat: no-repeat;}
        ul.req li.fail{background-image:url('./out/src/img/fail.png');background-repeat: no-repeat;}
        ul.req li.null{background-image:url('./out/src/img/null.png');background-repeat: no-repeat;}
        ul.req ul{padding:0;margin:0;}
        ul.req li.group {border:none;float:left;font-weight:bold;width:28%;}
        ul.req li.clear{clear:left;diplay:none;border:none;visibility:collapse;height:0px;padding:0;margin:0;display:block;line-height: 0;}
    </style>

    <?php
        if ( ( $iRedir2Step = $this->getNextSetupStep() ) !== null ) {
            ?><meta http-equiv="refresh" content="3; URL=index.php?istep=<?php echo $iRedir2Step;?>&sid=<?php $this->getSid();?>"><?php
        }
    ?>
</head>

<body>

<div id="page">
    <a href="index.php?istep=<?php $this->getSetupStep('STEP_SYSTEMREQ' ); ?>&sid=<?php $this->getSid(); ?>"><img src="<?php echo $this->getImageDir(); ?>/setup_logo.png" alt="OXID eSales" hspace="5" vspace="5" border="0"></a>
    <div id="header">
        <?php
        $iCntr = 0;
        foreach ( $this->getSetupSteps() as $iTab ) :
            // only "real" steps
            if ( fmod( $iTab, 100 ) ) {
                continue;
            }

            $blAct = ( floor( $this->getCurrentSetupStep() / 100 ) == ( $iTab / 100 ) );
            $iStepId = floor( $iTab / 100 ) - 1;
            $iCntr++;

            $sTabClass = $sTabLinkOpen = $sTabLinkClose = '';
            if ( $blAct ) {
                $sTabClass     = 'act';
                $sTabLinkOpen  = '<a href="index.php?istep='.$iTab.'&sid='.$this->getSid(false).'">';
                $sTabLinkClose = '</a>';
            }
         ?>
            <dl class="tab <?php echo $sTabClass; ?>">
                <dt><?php echo $sTabLinkOpen ?><?php echo $iCntr ,'. ',$this->getText( 'TAB_'.$iStepId.'_TITLE', false ); ?><?php echo $sTabLinkClose?></dt>
                <dd><?php echo $sTabLinkOpen ?><?php $this->getText( 'TAB_'.$iStepId.'_DESC'); ?><?php echo $sTabLinkClose?></dd>
            </dl>
        <?php
        endforeach;
        ?>
    </div>

    <div id="body">
    <?php
    $aMessages = $this->getMessages();
    foreach ( $this->getMessages() as $sMessage ) {
        ?><br><b><?php echo $sMessage;?></b><?php
    }
    if ( count( $aMessages ) ) {
        ?><br><br><?php
    }

    if ( ( $iRedir2Step = $this->getNextSetupStep() ) !== null ) {
        ?><br><br><?php $this->getText( 'HEADER_TEXT_SETUP_NOT_RUNS_AUTOMATICLY');?>
        <a href="index.php?istep=<?php echo $iRedir2Step;?>&sid=<?php $this->getSid();?>" id="continue"><b><?php $this->getText( 'HERE' ); ?></b></a>.<br><br><?php
    }
