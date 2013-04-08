[{include file="headitem.tpl" title="ADMINGB_TITLE"|oxmultilangassign box="list"}]

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


<form name="search" id="search" action="[{ $oViewConf->getSelfLink() }]" method="post">
[{include file="_formparams.tpl" cl="adminguestbook_list" lstrt=$lstrt actedit=$actedit oxid=$oxid fnc="" language=$actlang editlanguage=$actlang}]
<table cellspacing="0" cellpadding="0" border="0" width="100%">
    <colgroup>
        [{block name="admin_adminguestbook_list_colgroup"}]
            <col width="10%">
            <col width="15%">
            <col width="63%">
            <col width="2%">
        [{/block}]
    </colgroup>
    <tr>
        [{block name="admin_adminguestbook_list_filter"}]
            <td class="listfilter first" height="15"><div class="r1"><div class="b1">&nbsp;</div></div></td>
            <td class="listfilter"><div class="r1"><div class="b1">&nbsp;</div></div></td>
            <td class="listfilter" colspan="2"><div class="r1"><div class="b1">&nbsp;</div></div></td>
        [{/block}]
    </tr>
    <tr>
        [{block name="admin_adminguestbook_list_sorting"}]
            <td class="listheader first" height="15" >[{ oxmultilang ident="GENERAL_DATE" }]</td>
            <td class="listheader">[{ oxmultilang ident="ADMINGB_LIST_AUTHOR" }]</td>
            <td class="listheader" colspan="2">[{ oxmultilang ident="ADMINGB_LIST_ENTRY" }]</td>
        [{/block}]
    </tr>

[{assign var="blWhite" value=""}]
[{assign var="_cnt" value=0}]
[{foreach from=$mylist item=listitem}]
    [{assign var="_cnt" value=$_cnt+1}]
    <tr id="row.[{$_cnt}]">

        [{block name="admin_adminguestbook_list_item"}]
            [{ if $listitem->blacklist == 1}]
                [{assign var="listclass" value=listitem3 }]
            [{ else}]
                [{assign var="listclass" value=listitem$blWhite }]
            [{ /if}]
            [{ if $listitem->getId() == $oxid }]
                [{assign var="listclass" value=listitem4 }]
            [{ /if}]
            <td valign="top" class="[{ $listclass}][{if !$listitem->oxgbentries__oxviewed->value && $listitem->getId() != $oxid }]new[{/if}]" height="15"><div class="listitemfloating">&nbsp;<a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxgbentries__oxid->value}]');" class="[{ $listclass}][{if !$listitem->oxgbentries__oxviewed->value && $listitem->getId() != $oxid}]new[{/if}]">[{ $listitem->oxgbentries__oxcreate|oxformdate }]</a></div></td>
            <td valign="top" class="[{ $listclass}][{if !$listitem->oxgbentries__oxviewed->value && $listitem->getId() != $oxid}]new[{/if}]"><div class="listitemfloating">&nbsp;<a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxgbentries__oxid->value}]');" class="[{ $listclass}][{if !$listitem->oxgbentries__oxviewed->value && $listitem->getId() != $oxid}]new[{/if}]">[{ $listitem->oxuser__oxfname->value }] [{ $listitem->oxuser__oxlname->value }]</a></div></td>
            <td valign="top" class="[{ $listclass}][{if !$listitem->oxgbentries__oxviewed->value && $listitem->getId() != $oxid}]new[{/if}]"><div class="listitemfloating">&nbsp;<a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxgbentries__oxid->value}]');" class="[{ $listclass}][{if !$listitem->oxgbentries__oxviewed->value && $listitem->getId() != $oxid}]new[{/if}]">[{ $listitem->oxgbentries__oxcontent->value|oxtruncate:300:"..":false  }]</a></div></td>
            <td  class="[{ $listclass}]">[{if !$readonly}]<a href="Javascript:top.oxid.admin.deleteThis('[{ $listitem->oxgbentries__oxid->value }]');" class="delete" id="del.[{$_cnt}]" [{include file="help.tpl" helpid=item_delete}]></a>[{/if}]</td>
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
    parent.parent.sMenuItem    = "[{ oxmultilang ident="ADMINGB_LIST_MENUITEM" }]";
    parent.parent.sMenuSubItem = "[{ oxmultilang ident="ADMINGB_LIST_MENUSUBITEM" }]";
    parent.parent.sWorkArea    = "[{$_act}]";
    parent.parent.setTitle();
}
</script>
</body>
</html>