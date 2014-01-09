[{include file="headitem.tpl" title="OXDIAG_MAIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
    <!--

    var blIsHelpVisible = false;

    function handleSubmit()
    {
        var oButton = document.getElementById("submitButton");
        oButton.disabled = true;
    }

    function handleCheck()
    {
        var oCheckbox = document.getElementById("oxdiag_frm_chkvers");
        var oListAll = document.getElementById("listAllFiles");

        oListAll.disabled = (!oCheckbox.checked);
        if (!oCheckbox.checked) {
            oListAll.checked = false;
        }
    }

    function handleHelp()
    {
        blIsHelpVisible = !blIsHelpVisible;

        var oComment = document.getElementById("version_checker_comment");
        oComment.setAttribute( 'class', (blIsHelpVisible) ? 'selected checker_comment' : 'hidden' );
    }

    //-->
</script>

<style>

    .hidden {
        display: none;
    }

    .checker_comment {
        max-width: 600px;
        padding: 5px;
    }
    .result {
        padding: 15px;
        background-color: #F0F0F0 !important;
        border: 1px solid #C0C0C0 !important;
    }

    .selected {
        background-color: #F0F0F0 !important;
        border: 1px solid #C0C0C0 !important;
    }

</style>

<h1>[{oxmultilang ident='OXDIAG_HOME'}]</h1>

<p>[{oxmultilang ident='OXDIAG_ABOUT'}]</p>

<table>
    <tr>
        <td valign="top">

            [{ if !empty($sErrorMessage) }]
                <p><span style="color: red"><b>[{ oxmultilang ident="OXDIAG_ERRORMESSAGETEMPLATE" }]</b></span></p>
                <span style="color: red">[{ $sErrorMessage }]</span>
            [{elseif !$oView->getParam('runAnalysis')}]

            <form name="diagnosticsForm" id="diagnosticsForm" action="[{ $oViewConf->getSelfLink() }]" onsubmit="handleSubmit()" method="post">
                <table border="0" cellpadding="0">
                    [{$oViewConf->getHiddenSid()}]
                    <input type="hidden" name="cl" value="diagnostics_main">
                    <input type="hidden" name="fnc" value="startDiagnostics">

                    <input type="hidden" name="runAnalysis" value="1">

                    <tr>
                        <td><input type="checkbox" id="oxdiag_frm_modules" name="oxdiag_frm_modules" value="1" checked></td>
                        <td><label for="oxdiag_frm_modules">[{oxmultilang ident='OXDIAG_COLLECT_MODULES'}]</label></td>
                    </tr>

                    <tr>
                        <td><input type="checkbox" id="oxdiag_frm_health" name="oxdiag_frm_health" value="1" checked></td>
                        <td><label for="oxdiag_frm_health">[{oxmultilang ident='OXDIAG_COLLECT_HEALTH'}]</label></td>
                    </tr>

                    <tr>
                        <td><input type="checkbox" id="oxdiag_frm_php" name="oxdiag_frm_php" value="1" checked></td>
                        <td><label for="oxdiag_frm_php">[{oxmultilang ident='OXDIAG_COLLECT_PHP'}]</label></td>
                    </tr>

                    <tr>
                        <td><input type="checkbox" id="oxdiag_frm_server" name="oxdiag_frm_server" value="1" checked></td>
                        <td><label for="oxdiag_frm_server">[{oxmultilang ident='OXDIAG_COLLECT_SERVER'}]</label></td>
                    </tr>

                    <tr>
                        <td><input type="checkbox" id="oxdiag_frm_chkvers" name="oxdiag_frm_chkvers" onchange="handleCheck();" value="1"></td>
                        <td id="labelCell"><label for="oxdiag_frm_chkvers">[{oxmultilang ident='OXDIAG_COLLECT_CHKVERS'}]</label>
                            <input type="button" id="helpBtn_chkvers" class="btnShowHelpPanel" onclick="handleHelp()">
                        </td>
                    </tr>
                    <tr><td></td><td><small>[{oxmultilang ident='OXDIAG_COLLECT_CHKVERS_DURATION'}]</small></td></tr>

                    <tr>
                        <td>&nbsp;</td>
                        <td><input type="checkbox" name="listAllFiles" value="listAllFiles" id="listAllFiles" disabled="true"> <label for="listAllFiles">[{oxmultilang ident='OXDIAG_FORM_LIST_ALL_FILES'}]</label></td>
                    </tr>
                </table>

                <br><br>
                <input type="submit" class="edittext" id="submitButton" name="submitButton" value=" [{ oxmultilang ident="OXDIAG_FORM_START_CHECK" }] " >

            </form>
            [{ /if}]

        </td>
        <td valign="top" >
            <div class="hidden" id="version_checker_comment">
            <p>[{ oxmultilang ident="OXDIAG_INTROINFORMATION" }]</p>
            <p>[{ oxmultilang ident="OXDIAG_INTROINFORMATION_NO_PERSONAL_INFO" }]</p>
            <p>[{ oxmultilang ident="OXDIAG_INTROINFORMATION_DATA_TRANSMITTED" }]</p>
            <ul>
                <li>[{ oxmultilang ident="OXDIAG_INTROINFORMATION_FILENAME_TO_BE_CHECKED" }]</li>
                <li>[{ oxmultilang ident="OXDIAG_INTROINFORMATION_MD5_CHECKSUM" }]</li>
                <li>[{ oxmultilang ident="OXDIAG_INTROINFORMATION_VERSION_DETECTED" }]</li>
                <li>[{ oxmultilang ident="OXDIAG_INTROINFORMATION_REVISION_DETECTED" }]</li>
            </ul>
            <p>[{ oxmultilang ident="OXDIAG_INTROINFORMATION_MORE_INFORMATION" }] <strong><a class="underlined" href="http://www.oxid-esales.com/de/news/blog/shop-checking-tool-oxchkversion-v3" target=_blank>[{ oxmultilang ident="OXDIAG_INTROINFORMATION_OXID_ESALES_BLOG" }]</a></strong>.</p>
            </div>
        </td>
    </tr>
</table>



[{ if !empty($sResult) }]
<h1>[{ oxmultilang ident="OXDIAG_RESULT_SUCCESSFUL" }]</h1>
<h2><strong><a class="underlined" href="[{ $oViewConf->getSelfLink() }]&amp;cl=diagnostics_main&amp;fnc=downloadResultFile">[{ oxmultilang ident="OXDIAG_DOWNLOAD_FILE" }]</a></strong>.</h2>

<h3>[{ oxmultilang ident="OXDIAG_RESULT" }]:</h3>
<div class="result">
    <p>
    [{ $sResult }]
    </p>
</div>
[{ /if}]

[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]