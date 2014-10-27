[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign box="list"}]

[{ if $readonly}]
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


<form name="search" id="search" action="[{ $oViewConf->getSelfLink() }]" method="post">
[{include file="_formparams.tpl" cl="language_list" lstrt=$lstrt actedit=$actedit oxid=$oxid fnc="" language=$actlang editlanguage=$actlang}]
<table cellspacing="0" cellpadding="0" border="0" width="100%">
    <colgroup>
        [{block name="admin_language_list_colgroup"}]
            <col width="4%">
            <col width="5%">
            <col width="90%">
            <col width="1%">
        [{/block}]
    </colgroup>
    <tr class="listitem">
        [{block name="admin_language_list_filter"}]
            <td valign="top" class="listfilter first" align="center">
                <div class="r1"><div class="b1">
                </div></div>
            </td>
            <td valign="top" class="listfilter">
                <div class="r1"><div class="b1">
                </div></div>
            </td>
            <td valign="top" class="listfilter" colspan="2">
                <div class="r1"><div class="b1">
                </div></div>
            </td>
       [{/block}]
    </tr>

    <tr>
        [{block name="admin_language_list_sorting"}]
            <td class="listheader first" height="15" align="center"><a href="Javascript:top.oxid.admin.setSorting( document.forms.search, '', 'active', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="GENERAL_ARTICLE_OXACTIVE" }]</a></td>
            <td class="listheader" height="15"><a href="Javascript:top.oxid.admin.setSorting( document.forms.search, '', 'abbr', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="LANGUAGE_ABBERVATION" }]</a></td>
            <td class="listheader" height="15" colspan="2"><a href="Javascript:top.oxid.admin.setSorting( document.forms.search, '', 'name', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="GENERAL_LANGUAGE_NAME" }]</a></td>
        [{/block}]
    </tr>

[{assign var="blWhite" value=""}]
[{assign var="_cnt" value=0}]
[{foreach from=$mylist item=listitem}]
    [{assign var="_cnt" value=$_cnt+1}]
    <tr id="row.[{$_cnt}]">
        [{block name="admin_language_list_item"}]
            [{ if $listitem->blacklist == 1}]
                [{assign var="listclass" value=listitem3 }]
            [{ else}]
                [{assign var="listclass" value=listitem$blWhite }]
            [{ /if}]
            [{ if $listitem->oxid == $oxid }]
                [{assign var="listclass" value=listitem4 }]
            [{ /if}]
            <td valign="top" class="[{ $listclass}][{ if $listitem->active == 1}] active[{/if}]" height="15"><div class="listitemfloating">&nbsp;<a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxid}]');" class="[{ $listclass}]">
             &nbsp;
            </a></div></td>
            <td valign="top" class="[{ $listclass}]" height="15"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxid}]');" class="[{ $listclass}]">[{ $listitem->abbr }]</a></div></td>
            <td valign="top" class="[{ $listclass}]" height="15"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxid}]');" class="[{ $listclass}]">[{if $listitem->default}]<b>[{/if}][{ $listitem->name }][{if $listitem->default}]</b>[{/if}]</a></div></td>
            <td align="right" class="[{ $listclass}]">
            [{if !$readonly && !$listitem->default }]
            <a href="Javascript:top.oxid.admin.deleteThis('[{ $listitem->oxid }]');" class="delete" id="del.[{$_cnt}]" title="" [{include file="help.tpl" helpid=item_delete}]></a>
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
[{include file="pagenavisnippet.tpl" colspan="5"}]
</table>
</form>
</div>


[{include file="pagetabsnippet.tpl"}]

<script type="text/javascript">
if (parent.parent)
{   parent.parent.sShopTitle   = "[{$actshopobj->oxshops__oxname->getRawValue()|oxaddslashes}]";
    parent.parent.sMenuItem    = "[{ oxmultilang ident="LANGUAGE_LIST_MENUITEM" }]";
    parent.parent.sMenuSubItem = "[{ oxmultilang ident="LANGUAGE_LIST_MENUSUBITEM" }]";
    parent.parent.sWorkArea    = "[{$_act}]";
    parent.parent.setTitle();
}
</script>
</body>
</html>