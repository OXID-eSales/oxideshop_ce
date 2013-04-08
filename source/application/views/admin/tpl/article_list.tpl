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


<form name="search" id="search" action="[{ $oViewConf->getSelfLink() }]" method="post">
[{include file="_formparams.tpl" cl="article_list" lstrt=$lstrt actedit=$actedit oxid=$oxid fnc="" language=$actlang editlanguage=$actlang}]
<table cellspacing="0" cellpadding="0" border="0" width="100%">
    <colgroup>
        [{block name="admin_article_list_colgroup"}]
            <col width="3%">
            <col width="10%">
            <col width="45%">
            <col width="30%">
            <col width="2%">
        [{/block}]
    </colgroup>
    <tr class="listitem">
        [{block name="admin_article_list_filter"}]
            <td valign="top" class="listfilter first" align="right">
                <div class="r1"><div class="b1">&nbsp;</div></div>
            </td>
            <td valign="top" class="listfilter" align="left">
                <div class="r1"><div class="b1">
                <input class="listedit" type="text" size="9" maxlength="128" name="where[oxarticles][oxartnum]" value="[{ $where.oxarticles.oxartnum}]">
                </div></div>
            </td>
            <td height="20" valign="middle" class="listfilter" nowrap>
                <div class="r1"><div class="b1">
                <select name="art_category" class="editinput" onChange="Javascript:document.search.lstrt.value=0;document.search.submit();">
                <option value="">[{ oxmultilang ident="ARTICLE_LIST_ALLPRODUCTS" }]</option>
                <optgroup label="[{ oxmultilang ident="GENERAL_CATEGORY" }]">
                [{foreach from=$cattree->aList item=pcat}]
                <option value="cat@@[{ $pcat->oxcategories__oxid->value }]" [{ if $pcat->selected}]SELECTED[{/if}]>[{ $pcat->oxcategories__oxtitle->getRawValue() }]</option>
                [{/foreach}]
                </optgroup>
                <optgroup label="[{ oxmultilang ident="GENERAL_MANUFACTURER" }]">
                [{foreach from=$mnftree item=pmnf}]
                <option value="mnf@@[{ $pmnf->oxmanufacturers__oxid->value }]" [{ if $pmnf->selected}]SELECTED[{/if}]>[{ $pmnf->oxmanufacturers__oxtitle->value }]</option>
                [{/foreach}]
                </optgroup>
                <optgroup label="[{ oxmultilang ident="GENERAL_VENDOR" }]">
                [{foreach from=$vndtree item=pvnd}]
                <option value="vnd@@[{ $pvnd->oxvendor__oxid->value }]" [{ if $pvnd->selected}]SELECTED[{/if}]>[{ $pvnd->oxvendor__oxtitle->value }]</option>
                [{/foreach}]
                </optgroup>
                </select>
                <select name="pwrsearchfld" class="editinput" onChange="Javascript:document.search.lstrt.value=0;top.oxid.admin.setSorting( document.search, 'oxarticles', this.value, 'asc');document.forms.search.submit();">
                    [{foreach from=$pwrsearchfields key=field item=desc}]
                    [{assign var="ident" value=GENERAL_ARTICLE_$desc}]
                    [{assign var="ident" value=$ident|oxupper }]
                    <option value="[{ $desc }]" [{ if $pwrsearchfld == $desc|oxupper }]SELECTED[{/if}]>[{ oxmultilang|oxtruncate:20:"..":true noerror=true alternative=$desc ident=$ident }]</option>
                    [{/foreach}]
                </select>
                <input class="listedit" type="text" size="20" maxlength="128" name="where[oxarticles][[{$pwrsearchfld|oxlower}]]" value="[{ $pwrsearchinput}]" [{include file="help.tpl" helpid=searchfieldoxdynamic}]>
                </div></div>
            </td>
            <td valign="top" class="listfilter" colspan="2" nowrap>
                <div class="r1"><div class="b1">
                <div class="find">
                <select name="changelang" class="editinput" onChange="Javascript:top.oxid.admin.changeLanguage();">
                [{foreach from=$languages item=lang}]
                <option value="[{ $lang->id }]" [{ if $lang->selected}]SELECTED[{/if}]>[{ $lang->name }]</option>
                [{/foreach}]
                </select>
                <input class="listedit" type="submit" name="submitit" value="[{ oxmultilang ident="GENERAL_SEARCH" }]" onClick="Javascript:document.search.lstrt.value=0;">
                </div>
                <input class="listedit" type="text" size="25" maxlength="128" name="where[oxarticles][oxshortdesc]" value="[{ $where.oxarticles.oxshortdesc}]" [{include file="help.tpl" helpid=searchfieldoxshortdesc}]>
                </div></div>
            </td>
        [{/block}]
    </tr>
    <tr class="listitem">
        [{block name="admin_article_list_sorting"}]
            <td class="listheader first" height="15" width="30" align="center"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'oxarticles', 'oxactive', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="GENERAL_ACTIVTITLE" }]</a></td>
            <td class="listheader"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'oxarticles', 'oxartnum', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="GENERAL_ARTNUM" }]</a></td>
            <td class="listheader" height="15">&nbsp;<a href="Javascript:top.oxid.admin.setSorting( document.search, 'oxarticles', '[{ $pwrsearchfld|oxlower }]', 'asc');document.search.submit();" class="listheader">[{assign var="ident" value=GENERAL_ARTICLE_$pwrsearchfld }][{assign var="ident" value=$ident|oxupper }][{ oxmultilang ident=$ident }]</a></td>
            <td class="listheader" colspan="2"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'oxarticles', 'oxshortdesc', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="GENERAL_SHORTDESC" }]</a></td>
        [{/block}]
    </tr>

[{assign var="blWhite" value=""}]
[{assign var="_cnt" value=0}]
[{foreach from=$mylist item=listitem}]
    [{assign var="_cnt" value=$_cnt+1}]
    <tr id="row.[{$_cnt}]">

    [{block name="admin_article_list_item"}]
        [{ if $listitem->blacklist == 1}]
            [{assign var="listclass" value=listitem3 }]
        [{ else}]
            [{assign var="listclass" value=listitem$blWhite }]
        [{ /if}]
        [{ if $listitem->oxarticles__oxid->value == $oxid }]
            [{assign var="listclass" value=listitem4 }]
        [{ /if}]
        <td valign="top" class="[{ $listclass}][{ if $listitem->oxarticles__oxactive->value == 1}] active[{/if}]" height="15"><div class="listitemfloating">&nbsp</a></div></td>
        <td valign="top" class="[{ $listclass}]"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxarticles__oxid->value }]');" class="[{ $listclass}]">[{ $listitem->oxarticles__oxartnum->value }]</a></div></td>
        <td valign="top" class="[{ $listclass}]" height="15"><div class="listitemfloating">&nbsp;<a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxarticles__oxid->value }]');" class="[{ $listclass}]">[{ $listitem->pwrsearchval|oxtruncate:200:"..":false }]</a></div></td>
        <td valign="top" class="[{ $listclass}]"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxarticles__oxid->value }]');" class="[{ $listclass}]">[{ $listitem->oxarticles__oxshortdesc->value|strip_tags|oxtruncate:45:"..":true }]</a></div></td>
        <td class="[{ $listclass}]">
          [{if !$readonly}]
              <a href="Javascript:top.oxid.admin.deleteThis('[{ $listitem->oxarticles__oxid->value }]');" class="delete" id="del.[{$_cnt}]"title="" [{include file="help.tpl" helpid=item_delete}]></a>
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
    parent.parent.sMenuItem    = "[{ oxmultilang ident="GENERAL_MENUITEM" }]";
    parent.parent.sMenuSubItem = "[{ oxmultilang ident="ARTICLE_LIST_MENUSUBITEM" }]";
    parent.parent.sWorkArea    = "[{$_act}]";
    parent.parent.setTitle();
}
</script>
</body>
</html>

