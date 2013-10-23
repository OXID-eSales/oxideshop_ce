[{capture append="oxidBlock_content"}]
[{assign var="template_title" value="PRODUCT_COMPARISON"|oxmultilangassign}]

[{ $oView->setNoPaging() }]

[{assign var="articleList" value=$oView->getCompArtList() }]
[{assign var="atributeList" value=$oView->getAttributeList() }]
[{assign var="currency" value=$oView->getActCurrency()}]

<h1 id="productComparisonHeader" class="pageHead">[{$template_title}]</h1>
<div>
[{if $oView->getCompareItemsCnt() > 0 }]
    [{oxscript include="js/libs/scrollpane/jscrollpane.min.js"}]
    [{oxscript include="js/libs/scrollpane/mousewheel.js"}]
    [{oxscript include="js/libs/scrollpane/mwheelIntent.js"}]
    [{oxstyle include="css/libs/jscrollpane.css"}]
    [{oxscript include="js/widgets/oxcompare.js" priority=10 }]
    [{oxscript add="$( '#compareList' ).oxCompare();"}]
    [{if $oView->getCompareItemsCnt() == 1 }]
        [{ oxmultilang ident="MESSAGE_SELECT_MORE_PRODUCTS" }]
    [{/if}]
    <table id="compareList">
        <tr>
            <td>
                <div id="compareFirstCol" [{if $oxcmp_user}]class="compareNarrowFirstCol"[{/if}]>
                    <table>
                        <tr id="firstDataTr">
                            <td class="js-firstCol">&nbsp;</td>
                        </tr>
                        <tr>
                            <td class="js-firstCol">[{ oxmultilang ident="PRODUCT_ATTRIBUTES" }]</td>
                        </tr>
                        [{foreach key=sAttrID from=$atributeList item=oAttrib name=CmpAttr}]
                        <tr>
                            <td class="js-firstCol" id="cmpAttrTitle_[{$smarty.foreach.CmpAttr.iteration}]">[{ $oAttrib->title }]:</td>
                        </tr>
                        [{/foreach}]
                    </table>
                </div>
            </td>
            <td>
                <div id="compareDataDiv" class="[{if $oxcmp_user}]compareNarrow[{else}]compareWide[{/if}]">
                    <table>
                        <tr id="firstTr">
                            [{foreach key=iProdNr from=$articleList item=product name=comparelist}]
                            <td class="alignTop">
                                [{if $oView->getCompareItemsCnt() > 1 }]
                                    <div class="lineBox clear">
                                    [{if !$product->hidePrev}]
                                        <a id="compareLeft_[{ $product->oxarticles__oxid->value }]" rel="nofollow" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl="|cat:$oViewConf->getActiveClassName() params="fnc=moveleft&amp;aid=`$product->oxarticles__oxnid->value`&amp;pgNr="|cat:$oView->getActPage() }]" class="navigation movePrev">&laquo;</a>
                                    [{/if}]
                                    [{ oxmultilang ident="MOVE" }]
                                    [{if !$product->hideNext}]
                                        <a id="compareRight_[{ $product->oxarticles__oxid->value }]" rel="nofollow" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl="|cat:$oViewConf->getActiveClassName() params="fnc=moveright&amp;aid=`$product->oxarticles__oxnid->value`&amp;pgNr="|cat:$oView->getActPage() }]" class="navigation moveNext">&raquo;</a>
                                    [{/if}]
                                    </div>
                                [{/if}]

                                [{oxid_include_widget cl="oxwArticleBox" _parent=$oView->getClassName() iLinkType=$product->getLinkType() nocookie=1 _navurlparams=$oViewConf->getNavUrlParams() _object=$product anid=$product->getId() altproduct=$altproduct iIndex=$smarty.foreach.comparelist.iteration sWidgetType=product sListType=compareitem inlist=$product->isInList() skipESIforUser=1}]
                            </td>
                            [{/foreach}]
                        </tr>
                        <tr>
                            [{foreach key=iProdNr from=$articleList item=product name=testArt}]
                            <td class="centered">
                            [{*  if $oxcmp_user }]
                                  <a id="tonotice_cmp_[{ $product->oxarticles__oxid->value }]_[{$smarty.foreach.testArt.iteration}]" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl="|cat:$oViewConf->getActiveClassName() params="aid=`$product->oxarticles__oxnid->value`&amp;anid=`$product->oxarticles__oxnid->value`&amp;fnc=tonoticelist&amp;am=1"|cat:$oViewConf->getNavUrlParams() }]" rel="nofollow">[{ oxmultilang ident="WISH_LIST" }]</a>
                                  [{if $oViewConf->getShowWishlist()}]
                                  <a id="towish_cmp_[{ $product->oxarticles__oxid->value }]_[{$smarty.foreach.testArt.iteration}]" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl="|cat:$oViewConf->getActiveClassName() params="aid=`$product->oxarticles__oxnid->value`&anid=`$product->oxarticles__oxnid->value`&amp;fnc=towishlist&amp;am=1"|cat:$oViewConf->getNavUrlParams() }]" rel="nofollow">[{ oxmultilang ident="GIFT_REGISTRY" }]</a>
                                  [{/if}]
                            [{/if *}]
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
                                          <button class="submitButton" id="remove_cmp_[{ $product->oxarticles__oxid->value }]" type="submit" title="[{ oxmultilang ident="REMOVE" }]" name="send">[{ oxmultilang ident="REMOVE" }]</button>
                                      [{/oxhasrights}]
                                  </div>
                                </form>
                            </td>
                            [{/foreach}]
                        </tr>
                        [{foreach key=sAttrID from=$atributeList item=oAttrib name=CmpAttr}]
                        <tr>
                            [{foreach key=iProdNr from=$articleList item=product}]
                            <td class="alignTop">
                              <div id="cmpAttr_[{$smarty.foreach.CmpAttr.iteration}]_[{ $product->oxarticles__oxid->value }]">
                                [{if $oAttrib->aProd.$iProdNr && $oAttrib->aProd.$iProdNr->value}]
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
                </div>
            </td>
        </tr>
    </table>

[{else}]
  [{ oxmultilang ident="MESSAGE_SELECT_AT_LEAST_ONE_PRODUCT" }]
[{/if}]
</div>
[{ insert name="oxid_tracker" title=$template_title }]
[{/capture}]

[{if !$oxcmp_user->oxuser__oxpassword->value}]
    [{include file="layout/page.tpl"}]
[{else}]
    [{capture append="oxidBlock_sidebar"}]
        [{include file="page/account/inc/account_menu.tpl" active_link="compare"}]
    [{/capture}]
    [{include file="layout/page.tpl" sidebar="Left"}]
[{/if}]