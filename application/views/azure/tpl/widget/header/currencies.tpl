[{oxscript include="js/widgets/oxflyoutbox.js" priority=10 }]
[{oxscript add="$( '#currencyTrigger' ).oxFlyOutBox();"}]
[{if $oView->loadCurrency()}]
    <div class="topPopList">
    [{capture name="currencyList"}]
        [{foreach from=$oxcmp_cur item=_cur}]
            [{if $_cur->selected}]
                [{assign var="selectedCurrency" value=$_cur->name}]
                [{capture name="currencySelected"}]
                    <a href="[{$_cur->link|oxaddparams:$oView->getDynUrlParams()}]" title="[{ $_cur->name }]" rel="nofollow"><span>[{ $_cur->name }]</span></a>
                [{/capture}]
            [{/if}]
            <li><a[{if $_cur->selected}] class="selected"[{/if}] href="[{$_cur->link|oxaddparams:$oView->getDynUrlParams()}]" title="[{ $_cur->name }]" rel="nofollow"><span>[{ $_cur->name }]</span></a>
        [{/foreach}]
    [{/capture}]
    <p id="currencyTrigger" class="selectedValue">
        [{$smarty.capture.currencySelected}]
    </p>
    <div class="flyoutBox">
    <ul id="currencies" class="corners">
        <li class="active">[{$smarty.capture.currencySelected}]</li>
        [{$smarty.capture.currencyList}]
    </ul>
    </div>
    </div>
[{/if}]
[{oxscript widget=$oView->getClassName()}]