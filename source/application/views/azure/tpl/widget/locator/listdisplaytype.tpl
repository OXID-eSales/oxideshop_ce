[{assign var="listType" value=$oView->getListDisplayType()}]
[{assign var="_additionalParams" value=$oView->getAdditionalParams()}]
[{assign var="_artPerPage" value=$oViewConf->getArtPerPageCount()}]
[{if $oView->canSelectDisplayType()}]
    [{oxscript include="js/widgets/oxdropdown.js" priority=10 }]
    [{oxscript add="$('div.dropDown p').oxDropDown();"}]
    <div class="dropDown js-fnLink" id="viewOptions">
        <p>
            <label>[{oxmultilang ident="LIST_DISPLAY_TYPE" suffix="COLON" }]</label>
            <span>[{oxmultilang ident=$listType}]</span>
        </p>
        <ul class="drop FXgradGreyLight shadow">
            <li><a href="[{$oView->getLink()|oxaddparams:"ldtype=infogrid&amp;_artperpage=$_artPerPage&amp;pgNr=0&amp;$_additionalParams"}]" [{if $listType eq 'infogrid' }]class="selected" [{/if}]>[{oxmultilang ident="infogrid"}]</a></li>
            <li><a href="[{$oView->getLink()|oxaddparams:"ldtype=grid&amp;_artperpage=$_artPerPage&amp;pgNr=0&amp;$_additionalParams"}]" [{if $listType eq 'grid' }]class="selected" [{/if}]>[{oxmultilang ident="grid"}]</a></li>
            <li><a href="[{$oView->getLink()|oxaddparams:"ldtype=line&amp;_artperpage=$_artPerPage&amp;pgNr=0&amp;$_additionalParams"}]" [{if $listType eq 'line' }]class="selected" [{/if}]>[{oxmultilang ident="line"}]</a></li>
        </ul>
    </div>
[{/if}]