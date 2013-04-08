[{include file="headitem.tpl" title="TOOLS_LIST_TITLE"|oxmultilangassign box="list"}]

<script type="text/javascript">
<!--
window.onload = function ()
{
    top.reloadEditFrame();
    [{ if $updatelist == 1}]
        top.oxid.admin.updateList('[{ $oxid }]');
    [{ /if}]
}
//-->
</script>
<form name="search" id="search" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="actedit" value="[{ $actedit }]">
    <input type="hidden" name="cl" value="tools_list">
    <input type="hidden" name="oxid" value="x">
</form>

<div id="liste">

    <table cellspacing="0" cellpadding="0" border="0">
    [{if isset($blViewSuccess)}]
    <tr>
        <td class="editnavigation">
            [{if $blViewSuccess}]
            [{ oxmultilang ident="TOOLS_LIST_UPDATEVIEWSSECCESS" }]
            [{else}]
            [{ oxmultilang ident="TOOLS_LIST_UPDATEVIEWSFAILED" }]
            [{/if}]
        </td>
    </tr>
    [{/if}]
    [{if $blMailSuccess}]
    <tr>
        <td class="editnavigation">[{ oxmultilang ident="TOOLS_LIST_SECCESS" }]</td>
    </tr>
    [{/if}]
    <tr>
    <td class="editnavigation">[{if $blFin}][{ oxmultilang ident="TOOLS_LIST_ACTIONEND" }][{/if}]</td>
    </tr>
    </table>
    <br>
    <table cellspacing="0" cellpadding="0" border="0" width="100%" class="editnavigation">
    [{foreach from=$aQueries key=key item=query}]
        [{ assign var="sQuery"        value=$aQueries.$key}]
        [{ assign var="sAffectedRows" value=$aAffectedRows.$key}]
        [{ assign var="sErrorMsg"     value=$aErrorMessages.$key}]
        [{ assign var="iErrorNum"     value=$aErrorNumbers.$key}]
        [{ if $sQuery }]
        <tr valign="top"><td>[{ oxmultilang ident="TOOLS_LIST_SQLQUERY" }] ([{$key+1}]) : </td><td>[{ $sQuery|oxwordwrap:100:"<br>":true }]</td></tr>
        [{/if}]
        [{ if $sAffectedRows }]
        <tr><td colspan="2"><br></td></tr>
        <tr valign="top"><td>[{ oxmultilang ident="TOOLS_LIST_AFFECTEDROWS" }] : </td><td>[{ $sAffectedRows }]</td></tr>
        [{/if}]
        [{ if $sErrorMsg }]
        <tr><td colspan="2"><br></td></tr>
        <tr valign="top"><td>[{ oxmultilang ident="TOOLS_LIST_ERRORMESSAGE" }] : </td><td>[{ $sErrorMsg }]</td></tr>
        [{/if}]
        [{ if $iErrorNum }]
        <tr><td colspan="2"><br></td></tr>
        <tr valign="top"><td>[{ oxmultilang ident="TOOLS_LIST_ERRORNUM" }] : </td><td>[{ $iErrorNum }]</td></tr>
        [{/if}]
        <tr><td colspan="2"><hr></td></tr>
    [{/foreach}]
    </table>

</div>

[{include file="pagetabsnippet.tpl" noOXIDCheck="true"}]

<script type="text/javascript">
if (parent.parent)
{   parent.parent.sShopTitle   = "[{$actshopobj->oxshops__oxname->getRawValue()|oxaddslashes}]";
    parent.parent.sMenuItem    = "[{ oxmultilang ident="TOOLS_LIST_MENUITEM" }]";
    parent.parent.sMenuSubItem = "[{ oxmultilang ident="TOOLS_LIST_MENUSUBITEM" }]";
    parent.parent.sWorkArea    = "[{$_act}]";
    parent.parent.setTitle();
}
</script>

[{include file="bottomitem.tpl"}]
