[{include file="headitem.tpl" title="TOOLS_MAIN_TITLE"|oxmultilangassign}]
[{ if $readonly }]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]
<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="oxidCopy" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="tools_main">
</form>

<form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="cl" value="tools_main">
    <input type="hidden" name="fnc" value="">
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="voxid" value="[{ $oxid }]">
    <input type="hidden" name="oxparentid" value="[{ $oxparentid }]">
    <input type="hidden" name="editval[oxarticles__oxid]" value="[{ $oxid }]">
</form>

<table cellspacing="0" cellpadding="0" border="0" width="98%">
<tr>
    <td valign="top" class="edittext">
        <form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" method="post" target="list" enctype="multipart/form-data">
        <input type="hidden" name="MAX_FILE_SIZE" value="[{$iMaxUploadFileSize}]">
        [{ $oViewConf->getHiddenSid() }]
        <input type="hidden" name="cl" value="tools_list">
        <input type="hidden" name="fnc" value="performsql">

        <table cellspacing="0" cellpadding="0" border="0" width="100%">
        <colgroup><col width="20%"><col width="80%"></colgroup>
        [{block name="admin_tools_main_form"}]
            <tr>
                <td class="edittext" valign="top">
                    [{ oxmultilang ident="TOOLS_MAIN_UPDATESQL" }]&nbsp;&nbsp;&nbsp;
                </td>
                <td class="edittext">
                    <textarea class="confinput" style="width: 100%; height: 120px" name="updatesql" [{ $readonly }]></textarea>
                    [{ oxinputhelp ident="HELP_TOOLS_MAIN_UPDATESQL" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                    [{ oxmultilang ident="TOOLS_MAIN_SQLDUMB" }] ([{ oxmultilang ident="GENERAL_MAX_FILE_UPLOAD"}] [{$sMaxFormattedFileSize}])&nbsp;&nbsp;&nbsp;
                </td>
                <td class="edittext"><br>
                    <input type="file" style="width: 370" class="edittext" name="myfile[SQL1@usqlfile]" [{ $readonly }]>
                    [{ oxinputhelp ident="HELP_TOOLS_MAIN_SQLDUMB" }]
                </td>
            </tr>
        [{/block}]
        <tr>
            <td class="edittext">
            </td>
            <td class="edittext"><br>
            <input type="submit" class="edittext" name="save" value="[{ oxmultilang ident="TOOLS_MAIN_START" }]" [{if !$blIsMallAdmin}]disabled[{/if}] [{ $readonly }]>
            </td>
        </tr>
        </table>
        </form>

    [{if $showViewUpdate}]
      <hr>
      <form name="regerateviews" id="regerateviews" action="[{ $oViewConf->getSelfLink() }]" method="post" target="list">
        [{ $oViewConf->getHiddenSid() }]
        <input type="hidden" name="cl" value="tools_list">
        <input type="hidden" name="fnc" value="updateViews">
        <br>[{ oxmultilang ident="TOOLS_MAIN_UPDATEVIEWSINFO" }]<br><br>
        <input class="confinput" type="Submit" value="[{ oxmultilang ident="TOOLS_MAIN_UPDATEVIEWSNOW" }]" onClick="return confirm('[{ oxmultilang ident="TOOLS_MAIN_UPDATEVIEWSCONFIRM" }]')" [{$readonly}]>
      </form>
    [{/if}]

    </td>
    <td valign="top" class="edittext" align="left">
    <br>
        <table cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td class="edittext">
            </td>
        </tr>
        </table>

    </td>
    </tr>
</table>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]