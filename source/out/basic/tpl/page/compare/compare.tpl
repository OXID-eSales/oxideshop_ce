[{assign var="template_title" value="COMPARE_TITLE"|oxmultilangassign}]
[{include file="_header.tpl" title=$template_title location=$template_title}]

[{if $oxcmp_user->oxuser__oxpassword->value}]
    [{include file="inc/account_header.tpl" active_link=7}]<br />
[{/if}]

[{if $oView->getCompArtList() }]
  <!-- page locator -->
  [{include file="inc/compare_locator.tpl" where="Top" }]
[{/if}]

  <strong id="test_productComparisonHeader" class="boxhead">[{$template_title}]</strong>
  <div class="box compare">
    [{if $oView->getCompArtList() }]
      [{assign var="colWidth" value="25" }]
       [{assign var="colCount" value=$oView->getCompArtList()|@count }]
       [{assign var="showFirstCol" value=$oView->getAttributeList()|@count}]

      [{if $showFirstCol}]
        [{math equation="a/(b + c)" a=100 b=$colCount c=1 format="%.2f" assign=colWidth}]
        [{math equation="a + b" a=1 b=$colCount assign=colCount}]
      [{else}]
        [{math equation="a/(b + c)" a=100 b=$colCount c=0 format="%.2f" assign=colWidth}]
        [{math equation="a + b" a=0 b=$colCount assign=colCount}]
      [{/if}]

  <table class="cmp_tbl">
    <colgroup>
      <col width="[{$colWidth}]%" span="[{$colCount}]">
    </colgroup>

    [{assign var="isFirst" value=$showFirstCol}]

    <tr class="no_bot_brd">
      [{foreach key=iProdNr from=$oView->getCompArtList() item=product name=comparelist}]
        [{if $isFirst}]
          <td class="no_left_brd">&nbsp;</td>
          [{assign var="isFirst" value=false}]
        [{/if}]
        <td valign="top">
          <div class="reorder">
            [{if !$product->hidePrev}]
            <div class="left">
                <a id="compareLeft_[{ $product->oxarticles__oxid->value }]" rel="nofollow" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl="|cat:$oViewConf->getActiveClassName() params="fnc=moveleft&amp;aid=`$product->oxarticles__oxnid->value`&amp;pgNr="|cat:$oView->getActPage() }]" class="prevnext">&laquo;</a>
            </div>
            [{/if}]

            [{if !$product->hideNext}]
            <div class="right">
                <a id="compareRight_[{ $product->oxarticles__oxid->value }]" rel="nofollow" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl="|cat:$oViewConf->getActiveClassName() params="fnc=moveright&amp;aid=`$product->oxarticles__oxnid->value`&amp;pgNr="|cat:$oView->getActPage() }]" class="prevnext">&raquo;</a>
            </div>
            [{/if}]
          </div>

          [{include file="inc/product.tpl" product=$product size="small" testid="cmp_`$product->oxarticles__oxid->value`_`$smarty.foreach.comparelist.iteration`"}]
        </td>
      [{/foreach}]
    </tr>

  <tr>
    [{assign var="isFirst" value=$showFirstCol}]
    [{foreach key=iProdNr from=$oView->getCompArtList() item=product name=testArt}]
      [{if $isFirst}]
        <th class="no_left_brd">[{ oxmultilang ident="COMPARE_PRODUCTATTRIBUTES" }]</th>
        [{assign var="isFirst" value=false}]
      [{/if}]
      <td align="center">
        [{ if $oxcmp_user }]
            <div class="actions">
              <a id="test_tonotice_cmp_[{ $product->oxarticles__oxid->value }]_[{$smarty.foreach.testArt.iteration}]" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl="|cat:$oViewConf->getActiveClassName() params="aid=`$product->oxarticles__oxnid->value`&amp;anid=`$product->oxarticles__oxnid->value`&amp;fnc=tonoticelist&amp;am=1"|cat:$oViewConf->getNavUrlParams() }]" rel="nofollow">[{ oxmultilang ident="COMPARE_NOTICELIST" }]</a>
              [{if $oViewConf->getShowWishlist()}]
                <a id="test_towish_cmp_[{ $product->oxarticles__oxid->value }]_[{$smarty.foreach.testArt.iteration}]" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl="|cat:$oViewConf->getActiveClassName() params="aid=`$product->oxarticles__oxnid->value`&anid=`$product->oxarticles__oxnid->value`&amp;fnc=towishlist&amp;am=1"|cat:$oViewConf->getNavUrlParams() }]" rel="nofollow">[{ oxmultilang ident="COMPARE_WISHLIST" }]</a>
              [{/if}]
            </div>
        [{/if}]
        <form action="[{ $oViewConf->getSelfActionLink() }]" method="post">
          <div>
              [{ $oViewConf->getHiddenSid() }]
              [{ $oViewConf->getNavFormParams() }]
              <input type="hidden" name="cl" value="[{ $oViewConf->getActiveClassName() }]">
              <input type="hidden" name="fnc" value="tocomparelist">
              <input type="hidden" name="aid" value="[{ $product->oxarticles__oxid->value }]">
              <input type="hidden" name="anid" value="[{ $product->oxarticles__oxnid->value }]">
              <input type="hidden" name="pgNr" value="0">
              <input type="hidden" name="am" value="1">
              <input type="hidden" name="removecompare" value="1">
              [{oxhasrights ident="TOBASKET"}]
              <div class="fromcompare" >
                 <input id="test_remove_cmp_[{ $product->oxarticles__oxid->value }]" type="submit" value="[{ oxmultilang ident="COMPARE_REMOVE" }]" name="send">
              </div>
              [{/oxhasrights}]
          </div>
        </form>
      </td>
    [{/foreach}]
  </tr>

  [{foreach key=sAttrID from=$oView->getAttributeList() item=oAttrib name=CmpAttr}]
    <tr>
    [{assign var="isFirst" value=$showFirstCol}]
      [{foreach key=iProdNr from=$oView->getCompArtList() item=product}]
        [{if $isFirst}]
          <th id="test_cmpAttrTitle_[{$smarty.foreach.CmpAttr.iteration}]" class="no_left_brd">[{ $oAttrib->title }]:</th>
          [{assign var="isFirst" value=false}]
        [{/if}]
        <td valign="top">
          <div id="test_cmpAttr_[{$smarty.foreach.CmpAttr.iteration}]_[{ $product->oxarticles__oxid->value }]">
            [{ if $oAttrib->aProd.$iProdNr && $oAttrib->aProd.$iProdNr->value}]
              [{ $oAttrib->aProd.$iProdNr->value }]
            [{else}]
              -
            [{/if}]
          </div>
        </td>
      [{/foreach}]
    </tr>
  [{/foreach}]
  </table>
[{else}]
  [{ oxmultilang ident="COMPARE_SELECTATLEASTTWOART" }]
[{/if}]
  </div>


[{if $oView->getCompArtList() }]
  <!-- page locator -->
  [{include file="inc/compare_locator.tpl" where="Bottom" }]
[{/if}]

<div class="bar prevnext">
    <form action="[{ $oViewConf->getSelfActionLink() }]" name="order" method="post">
      <div>
          [{ $oViewConf->getHiddenSid() }]
          <input type="hidden" name="cl" value="start">
          <div class="right">
              <input id="test_BackToShop" type="submit" value="[{ oxmultilang ident="COMPARE_BACKTOSHOP" }]">
          </div>
      </div>
    </form>
</div>

[{ insert name="oxid_tracker" title=$template_title }]
[{include file="_footer.tpl"}]
