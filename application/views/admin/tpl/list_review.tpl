[{include file="headitem.tpl" title="SHOWLIST_TITLE"|oxmultilangassign box=" "}]

[{assign var="where" value=$oView->getListFilter()}]
[{assign var="whereparam" value=""}]
[{foreach from=$where item=aField key=sTable}]
  [{foreach from=$aField item=sFilter key=sField}]
    [{assign var="whereparam" value=$whereparam|cat:"where["|cat:$sTable|cat:"]["|cat:$sField|cat:"]="|cat:$sFilter|cat:"&amp;"}]
  [{/foreach}]
[{/foreach}]
[{assign var="viewListSize" value=$oView->getViewListSize()}]
[{assign var="whereparam" value=$whereparam|cat:"viewListSize="|cat:$viewListSize}]

<script type="text/javascript">
<!--
function editThis ( sID )
{
    [{assign var="shMen" value=1}]

    [{foreach from=$menustructure item=menuholder }]
      [{if $shMen && $menuholder->nodeType == XML_ELEMENT_NODE && $menuholder->childNodes->length }]

        [{assign var="shMen" value=0}]
        [{assign var="mn" value=1}]

        [{foreach from=$menuholder->childNodes item=menuitem }]
          [{if $menuitem->nodeType == XML_ELEMENT_NODE && $menuitem->childNodes->length }]
            [{ if $menuitem->getAttribute('id') == 'mxmanageprod' }]

              [{foreach from=$menuitem->childNodes item=submenuitem }]
                [{if $submenuitem->nodeType == XML_ELEMENT_NODE && $submenuitem->getAttribute('cl') == 'article' }]

                    if ( top && top.navigation && top.navigation.adminnav ) {
                        var _sbli = top.navigation.adminnav.document.getElementById( 'nav-1-[{$mn}]-1' );
                        var _sba = _sbli.getElementsByTagName( 'a' );
                        top.navigation.adminnav._navAct( _sba[0] );
                    }

                [{/if}]
              [{/foreach}]

            [{ /if }]
            [{assign var="mn" value=$mn+1}]

          [{/if}]
        [{/foreach}]
      [{/if}]
    [{/foreach}]

    var oTransfer = document.getElementById("transfer");
    oTransfer.oxid.value=sID;
    oTransfer.cl.value='article';
    oTransfer.submit();
}

function changeLanguage()
{
    var oList = document.getElementById("showlist");
    oList.language.value=oList.changelang.value;
    oList.editlanguage.value=oList.changelang.value;
    oList.submit();
}
//-->
</script>

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="">
    <input type="hidden" name="updatelist" value="1">
    <input type="hidden" name="language" value="[{ $actlang }]">
    <input type="hidden" name="editlanguage" value="[{ $actlang }]">
</form>

[{ if $noresult }]
    <span class="listitem">
        <b>[{ oxmultilang ident="SHOWLIST_NORESULTS" }]</b><br><br>
    </span>
[{/if}]

<div id="liste">
<form name="showlist" id="showlist" action="[{ $oViewConf->getSelfLink() }]" method="post">
[{ $oViewConf->getHiddenSid() }]
<input type="hidden" name="cl" value="list_review">
<input type="hidden" name="language" value="[{ $actlang }]">
<input type="hidden" name="editlanguage" value="[{ $actlang }]">

<table cellspacing="0" cellpadding="0" border="0" width="99%">
    <colgroup>
        [{block name="admin_list_review_colgroup"}]
            <col width="5%">
            <col width="70%">
            <col width="25%">
        [{/block}]
    </colgroup>
    <tr>
    [{block name="admin_list_review_filter"}]
    <td class="listfilter first">
        <div class="r1"><div class="b1">
        <input class="listedit" type="text" size="15" maxlength="128" name="where[oxreviews][oxcreate]" value="[{ $where.oxreviews.oxcreate }]">
        </div></div>
    </td>
    <td class="listfilter">
        <div class="r1"><div class="b1">
        <input class="listedit" type="text" size="15" maxlength="128" name="where[oxreviews][oxtext]" value="[{ $where.oxreviews.oxtext }]">
        </div></div>
    </td>
    <td class="listfilter" nowrap>
        <div class="r1"><div class="b1">
        <div class="find">
            <select name="changelang" class="editinput" onChange="Javascript:changeLanguage();">
              [{foreach from=$languages item=lang}]
              <option value="[{ $lang->id }]" [{ if $lang->selected}]SELECTED[{/if}]>[{ $lang->name }]</option>
              [{/foreach}]
            </select>
            <select name="viewListSize" class="editinput" onChange="JavaScript:top.oxid.admin.changeListSize()">
              <option value="50" [{ if $viewListSize == 50 }]SELECTED[{/if}]>50</option>
              <option value="100" [{ if $viewListSize == 100 }]SELECTED[{/if}]>100</option>
              <option value="200" [{ if $viewListSize == 200 }]SELECTED[{/if}]>200</option>
            </select>
            <input class="listedit" type="submit" name="submitit" value="[{ oxmultilang ident="GENERAL_SEARCH" }]">
        </div>

        <input class="listedit" type="text" size="15" maxlength="128" name="where[oxarticles][oxtitle]" value="[{ $where.oxarticles.oxtitle }]">

        </div>
      </div>
    </td>
    [{/block}]
</tr>
<tr>
    [{block name="admin_list_review_sorting"}]
        <td class="listheader first"><a href="javascript:top.oxid.admin.setSorting( document.forms.showlist, 'oxreviews', 'oxcreate', 'asc');document.forms.showlist.submit();" class="listheader">[{ oxmultilang ident="snpreviewlistoxcreate" }]</a></td>
        <td class="listheader"><a href="javascript:top.oxid.admin.setSorting( document.forms.showlist, 'oxreviews', 'oxtext', 'asc');document.forms.showlist.submit();" class="listheader">[{ oxmultilang ident="snpreviewlistoxtext" }]</a></td>
        <td class="listheader"><a href="javascript:top.oxid.admin.setSorting( document.forms.showlist, '', 'arttitle', 'asc');document.forms.showlist.submit();" class="listheader">[{ oxmultilang ident="snpreviewlistoxtitle" }]</a></td>
    [{/block}]
</tr>

[{assign var="blWhite" value=""}]
[{assign var="_cnt" value=0}]
[{ foreach from=$mylist item=oReview }]
    [{assign var="_cnt" value=$_cnt+1}]
    <tr id="row.[{$_cnt}]">
        [{block name="admin_list_review_item"}]
            <td align="center" class="listitem[{ $blWhite }]" valign="top"><a href="Javascript:editThis( '[{$oReview->oxreviews__oxobjectid->value}]');" class="listitem[{ $blWhite }]">[{ $oReview->oxreviews__oxcreate|oxformdate }]</a></td>
            <td class="listitem[{ $blWhite }]" valign="top"><a href="Javascript:editThis( '[{$oReview->oxreviews__oxobjectid->value}]');" class="listitem[{ $blWhite }]">[{ $oReview->oxreviews__oxtext->value }]</a></td>
            <td class="listitem[{ $blWhite }]" valign="top"><a href="Javascript:editThis( '[{$oReview->oxreviews__oxobjectid->value}]');" class="listitem[{ $blWhite }]">[{if $oReview->oxreviews__oxparentid->value}][{ $oReview->oxreviews__parenttitle->value }] [{ $oReview->oxreviews__oxvarselect->value }][{else}][{$oReview->oxreviews__oxtitle->value}][{/if}]</a></td>
        [{/block}]
    </tr>
[{if $blWhite == "2"}]
    [{assign var="blWhite" value=""}]
[{else}]
    [{assign var="blWhite" value="2"}]
[{/if}]
[{/foreach}]
[{include file="pagenavisnippet.tpl" colspan="8"}]

</table>
</form>

</div>

<script type="text/javascript">
if (parent.parent)
{   parent.parent.sShopTitle   = "[{$actshopobj->oxshops__oxname->getRawValue()|oxaddslashes}]";
    parent.parent.sMenuItem    = "[{ oxmultilang ident="GENERAL_MENUITEM" }]";
    parent.parent.sMenuSubItem = "[{ oxmultilang ident="snpreviewlistheader" }]";
    parent.parent.sWorkArea    = "[{$_act}]";
    parent.parent.setTitle();
}
</script>
</body>
</html>
