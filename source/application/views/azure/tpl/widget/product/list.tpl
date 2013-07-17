[{ if !$type}]
    [{assign var="type" value="infogrid"}]
[{/if}]
[{ if $type=="line" || $type=="infogrid" }]
    [{oxscript include="js/widgets/oxcenterelementonhover.js" priority=10 }]
    [{oxscript add="$( '.pictureBox' ).oxCenterElementOnHover();" }]
[{/if}]

[{oxscript add="$('a.js-external').attr('target', '_blank');"}]
[{if $head}]
    [{if $header eq "light"}]
        <h3 class="lightHead sectionHead">[{$head}]</h3>
    [{else}]
        <h2 class="sectionHead clear">
            <span>[{$head}]</span>
            [{if $rsslink}]
                    <a class="rss js-external" id="[{$rssId}]" href="[{$rsslink.link}]" title="[{$rsslink.title}]"><img src="[{$oViewConf->getImageUrl('rss.png')}]" alt="[{$rsslink.title}]"><span class="FXgradOrange corners glowShadow">[{$rsslink.title}]</span></a>
            [{/if}]
        </h2>
    [{/if}]
[{/if}]
[{if $products|@count gt 0}]
    <ul class="[{$type}]View clear" id="[{$listId}]">
        [{foreach from=$products item=_product name=productlist}]
            [{assign var="_listid" value=$listId|cat:"_"|cat:$smarty.foreach.productlist.iteration}]
            <li class="productData">
                [{ oxid_include_widget cl="oxwArticleBox" cur=$oViewConf->getActCurrency() _parent=$oView->getClassName() nocookie=1 _navurlparams=$oViewConf->getNavUrlParams() sProductId=$_product->getId() sWidgetType=product sListType=listitem_$type testid=$_listid blDisableToCart=$blDisableToCart }]
            </li>
            [{if ($type eq "infogrid" AND ($smarty.foreach.productlist.last) AND ($smarty.foreach.productlist.iteration % 2 != 0 )) }]
                <li class="productData"></li>
            [{/if}]
        [{/foreach}]
    </ul>
[{/if}]</div>