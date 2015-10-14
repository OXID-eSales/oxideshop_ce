[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign box=" "}]

<script type="text/javascript">
    if(top)
    {
        top.sMenuItem    = "[{oxmultilang ident="SYSTEMINFO_MENUITEM"}]";
        top.sMenuSubItem = "[{oxmultilang ident="SYSTEMINFO_MENUSUBITEM"}]";
        top.sWorkArea    = "[{$_act}]";
        top.setTitle();
    }
</script>

<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="oxidCopy" value="[{$oxid}]">
    <input type="hidden" name="cl" value="article_main">
    <input type="hidden" name="w" value="main">
</form>

<form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post">
[{$oViewConf->getHiddenSid()}]
<input type="hidden" name="cl" value="article_main">
<input type="hidden" name="fnc" value="">
<input type="hidden" name="oxid" value="[{$oxid}]">
<input type="hidden" name="voxid" value="[{$oxid}]">
<input type="hidden" name="oxparentid" value="[{$oxparentid}]">
<input type="hidden" name="editval[oxarticles__oxid]" value="[{$oxid}]">

</form><br /><br />
<div class="center">

[{if $isdemo}]
    <h1>[{oxmultilang ident="SYSTEMINFO_DEMOMODE"}]</h1>
[{/if}]

<table border="0" cellpadding="3" width="600">
<tr class="h">
    <th>[{oxmultilang ident="SYSTEMINFO_VARIABLE"}]</th>
    <th>[{oxmultilang ident="SYSTEMINFO_VALUE"}]</th>
</tr>
[{foreach key=name item=value from=$aSystemInfo}]
<tr>
    <td class="e">[{$name}]</td>
    <td class="v">[{$value}]</td>
</tr>
[{/foreach}]
</table>
</div>