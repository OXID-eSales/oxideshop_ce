[{block name="pagenavisnippet_main"}]
[{if $pagenavi}]

  [{assign var="linkSort" value=""}]
  [{foreach from=$oView->getListSorting() item=aField key=sTable}]
    [{foreach from=$aField item=sSorting key=sField}]
      [{assign var="linkSort" value=$linkSort|cat:"sort["|cat:$sTable|cat:"]["|cat:$sField|cat:"]="|cat:$sSorting|cat:"&"}]
    [{/foreach}]
  [{/foreach}]

  [{assign var="where" value=$oView->getListFilter()}]
  [{assign var="whereparam" value="&"}]
  [{foreach from=$where item=aField key=sTable}]
    [{foreach from=$aField item=sFilter key=sField}]
      [{assign var="whereparam" value=$whereparam|cat:"where["|cat:$sTable|cat:"]["|cat:$sField|cat:"]="|cat:$sFilter|cat:"&"}]
    [{/foreach}]
  [{/foreach}]
  [{assign var="viewListSize" value=$oView->getViewListSize()}]
  [{assign var="whereparam" value=$whereparam|cat:"viewListSize="|cat:$viewListSize}]

<tr>
<td class="pagination" colspan="[{$colspan|default:"2"}]">
  <div class="r1">
    <div class="b1">

    <table cellspacing="0" cellpadding="0" border="0" width="100%">
      <tr>
        <td id="nav.site" class="pagenavigation" align="left" width="33%">
            [{oxmultilang ident="NAVIGATION_PAGE"}] [{$pagenavi->actpage}] / [{$pagenavi->pages}]
        </td>
        <td class="pagenavigation" height="22" align="center" width="33%">
           [{foreach key=iPage from=$pagenavi->changePage item=page}]
             <a id="nav.page.[{$iPage}]" class="pagenavigation[{if $iPage == $pagenavi->actpage}] pagenavigationactive[{/if}]" href="[{$oViewConf->getSelfLink() nofilter}]&cl=[{$oViewConf->getActiveClassName()}]&oxid=[{$oxid}]&jumppage=[{$iPage}]&[{$linkSort}]actedit=[{$actedit}]&language=[{$actlang}]&editlanguage=[{$actlang}][{$whereparam}]&folder=[{$folder}]&pwrsearchfld=[{$pwrsearchfld}]&art_category=[{$art_category}]">[{$iPage}]</a>
           [{/foreach}]
        </td>
        <td class="pagenavigation" align="right" width="33%">
          <a id="nav.first" class="pagenavigation" href="[{$oViewConf->getSelfLink() nofilter}]&cl=[{$oViewConf->getActiveClassName()}]&oxid=[{$oxid}]&jumppage=1&[{$linkSort}]&actedit=[{$actedit}]&language=[{$actlang}]&editlanguage=[{$actlang}][{$whereparam}]&folder=[{$folder}]&pwrsearchfld=[{$pwrsearchfld}]&art_category=[{$art_category}]">[{oxmultilang ident="GENERAL_LIST_FIRST"}]</a>
          <a id="nav.prev" class="pagenavigation" href="[{$oViewConf->getSelfLink() nofilter}]&cl=[{$oViewConf->getActiveClassName()}]&oxid=[{$oxid}]&jumppage=[{if $pagenavi->actpage-1 > 0}][{$pagenavi->actpage-1 > 0}][{else}]1[{/if}]&[{$linkSort}]&actedit=[{$actedit}]&language=[{$actlang}]&editlanguage=[{$actlang}][{$whereparam}]&folder=[{$folder}]&pwrsearchfld=[{$pwrsearchfld}]&art_category=[{$art_category}]">[{oxmultilang ident="GENERAL_LIST_PREV"}]</a>
          <a id="nav.next" class="pagenavigation" href="[{$oViewConf->getSelfLink() nofilter}]&cl=[{$oViewConf->getActiveClassName()}]&oxid=[{$oxid}]&jumppage=[{if $pagenavi->actpage+1 > $pagenavi->pages}][{$pagenavi->actpage}][{else}][{$pagenavi->actpage+1}][{/if}]&[{$linkSort}]&actedit=[{$actedit}]&language=[{$actlang}]&editlanguage=[{$actlang}][{$whereparam}]&folder=[{$folder}]&pwrsearchfld=[{$pwrsearchfld}]&art_category=[{$art_category}]">[{oxmultilang ident="GENERAL_LIST_NEXT"}]</a>
          <a id="nav.last" class="pagenavigation" href="[{$oViewConf->getSelfLink() nofilter}]&cl=[{$oViewConf->getActiveClassName()}]&oxid=[{$oxid}]&jumppage=[{$pagenavi->pages}]&[{$linkSort}]&actedit=[{$actedit}]&language=[{$actlang}]&editlanguage=[{$actlang}][{$whereparam}]&folder=[{$folder}]&pwrsearchfld=[{$pwrsearchfld}]&art_category=[{$art_category}]">[{oxmultilang ident="GENERAL_LIST_LAST"}]</a>
        </td>
      </tr>
    </table>
    </div>
  </div>
</td>
</tr>
[{/if}]
[{/block}]
