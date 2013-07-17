[{oxscript add="$('a.js-external').attr('target', '_blank');"}]
[{oxscript include="js/widgets/oxarticlebox.js" priority=10 }]
[{oxscript add="$( 'ul.js-articleBox' ).oxArticleBox();" }]
<div class="box" [{if $_boxId}]id="[{$_boxId}]"[{/if}]>
    [{if $_sHeaderIdent}]
        <h3 class="clear [{if $_sHeaderCssClass}] [{$_sHeaderCssClass}][{/if}]">
            [{ oxmultilang ident=$_sHeaderIdent }]
            [{assign var='rsslinks' value=$oView->getRssLinks() }]
            [{if $rsslinks.topArticles}]
                <a class="rss js-external" id="rssTopProducts" href="[{$rsslinks.topArticles.link}]" title="[{$rsslinks.topArticles.title}]"><img src="[{$oViewConf->getImageUrl('rss.png')}]" alt="[{$rsslinks.topArticles.title}]"><span class="FXgradOrange corners glowShadow">[{$rsslinks.topArticles.title}]</span></a>
            [{/if }]
        </h3>
    [{/if}]
    <ul class="js-articleBox featuredList">
    [{foreach from=$_oBoxProducts item=_oBoxProduct name=_sProdList}]
        [{oxid_include_widget cl="oxwArticleBox" _parent=$oView->getClassName() sProductId=$_oBoxProduct->getId() nocookie=1 sWidgetType=product sListType=boxproduct}]
    [{/foreach}]
    </ul>
</div>