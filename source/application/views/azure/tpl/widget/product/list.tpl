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
        [{* TYPE to send *}]
        [{assign var="_listtype" value="widget/product/listitem_"|cat:$type|cat:".tpl"}]


        [{foreach from=$products item=_product name=productlist}]

            [{assign var="_listid" value=$listId|cat:"_"|cat:$smarty.foreach.productlist.iteration}]

        [{* oxid_include_widget cl="oxwArticleBox" cur=$oViewConf->getActCurrency() _parent=$oView->getClassName() nocookie=1 _navurlparams=$oViewConf->getNavUrlParams() anid=$_product->getId() product=$_product oxwtemplate=$_listtype testid=$_listid blDisableToCart=$blDisableToCart *}]
        [{*
        <h5>[{ $_product->getId() }]</h5>
        <h5>[{ $_listtype }] </h5>
        <h5>[{ $_listid }] </h5>
        *}]
        [{ oxid_include_widget cl="oxwArticleBox" cur=$oViewConf->getActCurrency() _parent=$oView->getClassName() nocookie=1 _navurlparams=$oViewConf->getNavUrlParams() productId=$_product->getId() product=$_product oxwtemplate=$_listtype testid=$_listid blDisableToCart=$blDisableToCart }]

        [{*
        <li class="productData">[{include file="widget/product/listitem_"|cat:$type|cat:".tpl" product=$_product testid=$listId|cat:"_"|cat:$smarty.foreach.productlist.iteration blDisableToCart=$blDisableToCart}]</li>
            [{if ($type eq "infogrid" AND ($smarty.foreach.productlist.last) AND ($smarty.foreach.productlist.iteration % 2 != 0 )) }]
                <li class="productData"></li>
            [{/if}] *}]
        [{/foreach}]
    </ul>
[{/if}]</div>