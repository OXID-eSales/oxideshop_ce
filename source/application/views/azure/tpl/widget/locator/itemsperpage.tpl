[{assign var="_additionalParams" value=$oView->getAdditionalParams()}]
[{oxscript include="js/widgets/oxdropdown.js" priority=10 }]
[{oxscript add="$('div.dropDown p').oxDropDown();"}]
<div class="dropDown js-fnLink" id="itemsPerPage">
    <p>
        <label>[{oxmultilang ident="WIDGET_PRODUCT_LOCATOR_ARTICLE_PER_PAGE"}]</label>
        <span>
            [{if $oViewConf->getArtPerPageCount() }]
                [{ $oViewConf->getArtPerPageCount() }]
            [{else}]
                [{oxmultilang ident="WIDGET_LOCATOR_CHOOSE"}]
            [{/if}]
        </span>
    </p>
    <ul class="drop FXgradGreyLight shadow">
        [{foreach from=$oViewConf->getNrOfCatArticles() item=iItemsPerPage}]
            <li><a href="[{ $oView->getLink()|oxaddparams:"ldtype=$listType&amp;_artperpage=$iItemsPerPage&amp;pgNr=0&amp;$_additionalParams"}]" rel="nofollow" [{if $oViewConf->getArtPerPageCount() == $iItemsPerPage }] class="selected"[{/if}]>[{$iItemsPerPage}]</a></li>
        [{/foreach}]
    </ul>
</div>