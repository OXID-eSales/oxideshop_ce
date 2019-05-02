<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <title>[{$title}]</title>
  <meta http-equiv="Content-Type" content="text/html; charset=[{$charset}]">
  [{if isset($meta_refresh_sec,$meta_refresh_url)}]
  <meta http-equiv=Refresh content="[{$meta_refresh_sec}];URL=[{$meta_refresh_url|replace:"&amp;":"&"}]">
  [{/if}]
  <link rel="shortcut icon" href="[{$oViewConf->getBaseDir()}]favicon.ico">

  [{block name="admin_headitem_inccss"}]
        <link rel="stylesheet" href="[{$oViewConf->getResourceUrl()}]main.css">
        <link rel="stylesheet" href="[{$oViewConf->getResourceUrl()}]colors_[{$oViewConf->getEdition()|lower}].css">
        <link rel="stylesheet" type="text/css" href="[{$oViewConf->getResourceUrl()}]yui/build/assets/skins/sam/container.css">
  [{/block}]

  [{block name="admin_headitem_incjs"}]
      <script type="text/javascript" src="[{$oViewConf->getResourceUrl()}]yui/build/utilities/utilities.js"></script>
      <script type="text/javascript" src="[{$oViewConf->getResourceUrl()}]yui/build/container/container-min.js"></script>
      <script type="text/javascript" src="[{$oViewConf->getResourceUrl()}]yui/oxid-help.js"></script>
  [{/block}]

  [{block name="admin_headitem_js"}]
      <script type="text/javascript">
      <!--
        // standard messages
        var sUnassignMessage = "[{oxmultilang ident='GENERAL_YOUWANTTOUNASSIGN'}]";
        var sDeleteMessage   = "[{oxmultilang ident='GENERAL_YOUWANTTODELETE'}]";

        // class info
        var sDefClass = '[{$default_edit}]';
        var sActClass = '[{$actlocation}]';

        [{if $updatelist == 1}]
        window.onload = function ()
        {
            top.oxid.admin.updateList('[{$oxid}]');
        }
        [{/if}]


        var ajaxpopup = null;
        function showDialog( sParams )
        {
            ajaxpopup = window.open('[{$oViewConf->getSelfLink()|replace:"&amp;":"&"}]'+sParams, 'ajaxpopup', 'width=850,height=680,scrollbars=yes,resizable=yes');
        }

        function focusPopup()
        {
            if ( ajaxpopup )ajaxpopup.focus();
        }

        window.onclick = focusPopup;

        function cleanupLongDesc( sValue )
        {
            if ( sValue == '<br>' || sValue == '<br />' ) {
                return '';
            }
            return sValue;
        }

        function copyLongDesc( sIdent )
        {
            var textVal = null;
            try {
                if ( WPro.editors[sIdent] != null ) {
                   WPro.editors[sIdent].prepareSubmission();
                   textVal = cleanupLongDesc( WPro.editors[sIdent].getValue() );
                }
            } catch(err) {
                    var varEl = document.getElementById(sIdent);
                    if (varEl != null) {
                        textVal = cleanupLongDesc( varEl.value );
                    }
            }

            if (textVal == null) {
                var varName = 'editor_'+sIdent;
                var varEl = document.getElementById(varName);
                if (varEl != null) {
                    textVal = cleanupLongDesc( varEl.value );
                }
            }

            if (textVal != null) {
                var oTarget = document.getElementsByName( 'editval['+ sIdent + ']' );
                if ( oTarget != null && ( oField = oTarget.item( 0 ) ) != null ) {
                    oField.value = textVal;
                }
            }
        }
      -->
      </script>
  [{/block}]

</head>
<body>
[{include file="tooltips.tpl"}]
<div id="oxajax_data"></div>

<div class="[{$box|default:'box'}]" style="[{if !$box && !$bottom_buttons}]height: 90%;[{/if}]">
[{include file="inc_error.tpl" Errorlist=$Errors.default}]

<!-- Input help popup -->
<div id="helpTextContainer" class="yui-skin-sam">
    <div id="helpPanel"></div>
</div>