[{capture append="oxidBlock_content"}]
    <div>
    [{assign var="oNews" value=$oView->getNews()}]
    <h1 class="pageHead">[{$oView->getTitle()}]</h1>
    <div class="listRefine clear bottomRound">
    </div>
        [{if !empty($oNews)}]
        [{foreach from=$oNews item=oNewsEntry}]
            [{if !empty($oNewsEntry) && !empty($oNewsEntry->oxnews__oxshortdesc->value)}]
                <div>
                    <h3>
                        <span>[{$oNewsEntry->oxnews__oxdate->value|date_format:"%d.%m.%Y"}] - </span> [{$oNewsEntry->oxnews__oxshortdesc->value}]
                    </h3>
                    [{$oNewsEntry->getLongDesc()}]
                </div>
            [{/if}]
        [{/foreach}]
        [{else}]
            [{oxmultilang ident="LATEST_NEWS_NOACTIVENEWS"}]
        [{/if}]
    </div>
    [{include file="widget/locator/listlocator.tpl" locator=$oView->getPageNavigation() place="bottom"}]
[{/capture}]
[{include file="layout/page.tpl" sidebar="Left"}]
