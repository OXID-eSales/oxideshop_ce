[{include file="headitem.tpl" title="OXDIAG_MAIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
    <!--

    function handleSubmit()
    {
        var oButton = document.getElementById("submitButton");
        oButton.disabled = true;
    }
    //-->
</script>

<style>
    .result {
        padding: 15px;
        background-color: #F0F0F0 !important;
        border: 1px solid #C0C0C0 !important;
    }
</style>

<h1>[{oxmultilang ident='OXDIAG_HOME'}]</h1>

<p>[{oxmultilang ident='OXDIAG_ABOUT'}]</p>

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

[{include file="include/support_contact_form.tpl"}]

<table>
    <tr>
        <td valign="top">

            [{if !empty($sErrorMessage)}]
                <p><span style="color: red"><b>[{oxmultilang ident="OXDIAG_ERRORMESSAGETEMPLATE"}]</b></span></p>
                <span style="color: red">[{$sErrorMessage}]</span>
            [{elseif !$oView->getParam('runAnalysis')}]

            <form name="diagnosticsForm" id="diagnosticsForm" action="[{$oViewConf->getSelfLink()}]" onsubmit="handleSubmit()" method="post">
                <table border="0" cellpadding="0">
                    [{$oViewConf->getHiddenSid()}]
                    <input type="hidden" name="cl" value="diagnostics_main">
                    <input type="hidden" name="fnc" value="startDiagnostics">

                    <input type="hidden" name="runAnalysis" value="1">

                    <tr>
                        <td><input type="checkbox" id="oxdiag_frm_modules" name="oxdiag_frm_modules" value="1" checked [{$readonly}]></td>
                        <td><label for="oxdiag_frm_modules">[{oxmultilang ident='OXDIAG_COLLECT_MODULES'}]</label></td>
                    </tr>

                    <tr>
                        <td><input type="checkbox" id="oxdiag_frm_health" name="oxdiag_frm_health" value="1" checked [{$readonly}]></td>
                        <td><label for="oxdiag_frm_health">[{oxmultilang ident='OXDIAG_COLLECT_HEALTH'}]</label></td>
                    </tr>

                    <tr>
                        <td><input type="checkbox" id="oxdiag_frm_php" name="oxdiag_frm_php" value="1" checked [{$readonly}]></td>
                        <td><label for="oxdiag_frm_php">[{oxmultilang ident='OXDIAG_COLLECT_PHP'}]</label></td>
                    </tr>

                    <tr>
                        <td><input type="checkbox" id="oxdiag_frm_server" name="oxdiag_frm_server" value="1" checked [{$readonly}]></td>
                        <td><label for="oxdiag_frm_server">[{oxmultilang ident='OXDIAG_COLLECT_SERVER'}]</label></td>
                    </tr>
                </table>

                <br><br>
                <input type="submit" class="edittext" id="submitButton" name="submitButton" value=" [{oxmultilang ident="OXDIAG_FORM_START_CHECK"}] " [{$readonly}]>

            </form>
            [{/if}]

        </td>
    </tr>
</table>



[{if !empty($sResult)}]
<h1>[{oxmultilang ident="OXDIAG_RESULT_SUCCESSFUL"}]</h1>
<h2><strong><a class="underlined" href="[{$oViewConf->getSelfLink()}]&amp;cl=diagnostics_main&amp;fnc=downloadResultFile">[{oxmultilang ident="OXDIAG_DOWNLOAD_FILE"}]</a></strong>.</h2>

<h3>[{oxmultilang ident="OXDIAG_RESULT"}]:</h3>
<div class="result">
    <p>
    [{$sResult}]
    </p>
</div>
[{/if}]

[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]