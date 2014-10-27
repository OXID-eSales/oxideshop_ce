[{oxscript add="$('a.js-external').attr('target', '_blank');"}]
[{oxscript include="js/widgets/oxarticlebox.js" priority=10 }]
[{oxscript add="$( 'ul.js-articleBox' ).oxArticleBox();" }]
[{assign var="currency" value=$oView->getActCurrency()}]
<div class="box" [{if $_boxId}]id="[{$_boxId}]"[{/if}]>
    [{if $_sHeaderIdent}]
        <h3 class="clear [{if $_sHeaderCssClass}] [{$_sHeaderCssClass}][{/if}]">
            [{ oxmultilang ident=$_sHeaderIdent }]
            [{assign var='rsslinks' value=$oView->getRssLinks() }]
            [{if $rsslinks.topArticles}]
                <a class="rss js-external" id="rssTopProducts" href="[{$rsslinks.topArticles.link}]" title="[{$rsslinks.topArticles.title}]"><img src="[{$oViewConf->getImageUrl('rss.png')}]" alt="[{$rsslinks.topArticles.title}]"><span class="FXgradOrange corners glowShadow">[{$rsslinks.topArticles.title}]</span></a>
            [{/if}]
        </h3>
    [{/if}]
    <ul class="js-articleBox featuredList">
        [{foreach from=$_oBoxProducts item=_oBoxProduct name=_sProdList}]
            [{assign var="iProdCount" value=$smarty.foreach._sProdList.first}]
            [{oxid_include_widget cl="oxwArticleBox" _parent=$oView->getClassName() _navurlparams=$oViewConf->getNavUrlParams() iLinkType=$_oBoxProduct->getLinkType() _object=$_oBoxProduct anid=$_oBoxProduct->getId() isVatIncluded=$oView->isVatIncluded() iProdCount=$iProdCount nocookie=1 sWidgetType=product sListType=boxproduct inlist=$_oBoxProduct->isInList() skipESIforUser=1}]
        [{/foreach}]
    </ul>
</div>