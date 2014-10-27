[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign box="list"}]
[{assign var="where" value=$oView->getListFilter()}]

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

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


<div id="liste">

<table cellspacing="0" cellpadding="0" border="0" width="100%">
    <colgroup>
        [{block name="admin_attribute_list_colgroup"}]
            <col width="98%">
            <col width="2%">
        [{/block}]
    </colgroup>
<form name="search" id="search" action="[{ $oViewConf->getSelfLink() }]" method="post">
[{include file="_formparams.tpl" cl="attribute_list" lstrt=$lstrt actedit=$actedit oxid=$oxid fnc="" language=$actlang editlanguage=$actlang}]
<tr class="listitem">
    [{block name="admin_attribute_list_filter"}]
        <td valign="top" class="listfilter first" height="20" colspan="2">
            <div class="r1"><div class="b1">
            <div class="find">
                <select name="changelang" class="editinput" onChange="Javascript:top.oxid.admin.changeLanguage();">
                [{foreach from=$languages item=lang}]
                <option value="[{ $lang->id }]" [{ if $lang->selected}]SELECTED[{/if}]>[{ $lang->name }]</option>
                [{/foreach}]
                </select>
                <input class="listedit" type="submit" name="submitit" value="[{ oxmultilang ident="GENERAL_SEARCH" }]">
            </div>
            <input class="listedit" type="text" size="50" maxlength="128" name="where[oxattribute][oxtitle]" value="[{ $where.oxattribute.oxtitle }]">
            </div></div>
        </td>
    [{/block}]
</tr>
<tr>
    [{block name="admin_attribute_list_sorting"}]
        <td class="listheader first" height="15" colspan="2"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'oxattribute', 'oxtitle', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="GENERAL_NAME" }]</a></td>
    [{/block}]
</tr>

[{assign var="blWhite" value=""}]
[{assign var="_cnt" value=0}]
[{foreach from=$mylist item=listitem}]
    [{assign var="_cnt" value=$_cnt+1}]
    <tr id="row.[{$_cnt}]">

        [{block name="admin_attribute_list_item"}]
            [{if $listitem->blacklist == 1}]
                [{assign var="listclass" value=listitem3 }]
            [{else}]
                [{assign var="listclass" value=listitem$blWhite }]
            [{/if}]
            [{if $listitem->getId() == $oxid }]
                [{assign var="listclass" value=listitem4 }]
            [{/if}]
            <td valign="top" class="[{ $listclass}]" height="15"><div class="listitemfloating">&nbsp;<a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxattribute__oxid->value}]');" class="[{ $listclass}]">[{ if $listitem->oxattribute__oxtitle->value }][{ $listitem->oxattribute__oxtitle->value }][{else}]--[{/if}]</a></div></td>
            <td class="[{ $listclass}]">
              [{if !$readonly }]
                  <a href="Javascript:top.oxid.admin.deleteThis('[{ $listitem->oxattribute__oxid->value }]');" class="delete" id="del.[{$_cnt}]" title="" [{include file="help.tpl" helpid=item_delete}]></a>
              [{/if}]
            </td>
        [{/block}]
    </tr>
[{if $blWhite == "2"}]
[{assign var="blWhite" value=""}]
[{else}]
[{assign var="blWhite" value="2"}]
[{/if}]
[{/foreach}]
[{include file="pagenavisnippet.tpl"}]
</table>
</form>
</div>


[{include file="pagetabsnippet.tpl"}]

<script type="text/javascript">
if (parent.parent)
{   parent.parent.sShopTitle   = "[{$actshopobj->oxshops__oxname->getRawValue()|oxaddslashes}]";
    parent.parent.sMenuItem    = "[{ oxmultilang ident="GENERAL_MENUITEM" }]";
    parent.parent.sMenuSubItem = "[{ oxmultilang ident="ATTRIBUTE_LIST_MENUSUBITEM" }]";
    parent.parent.sWorkArea    = "[{$_act}]";
    parent.parent.setTitle();
}
</script>
</body>
</html>
