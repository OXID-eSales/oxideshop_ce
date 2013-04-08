[{oxscript include="js/widgets/oxdropdown.js" priority=10 }]
[{oxscript add="$('div.dropDown p').oxDropDown();" }]
[{assign var="oSelections" value=$oSelectionList->getSelections()}]
[{if $oSelections}]
<div class="dropDown [{$sJsAction}]">
    <p class="selectorLabel underlined [{if $editable === false}] js-disabled[{/if}]">
        <label>[{$oSelectionList->getLabel()}]:</label>
        [{assign var="oActiveSelection" value=$oSelectionList->getActiveSelection()}]
        [{if $oActiveSelection}]
            <span>[{$oActiveSelection->getName()}]</span>
        [{elseif !$blHideDefault}]
            <span [{if $blInDetails}]class="selectMessage"[{/if}]>
                [{if $sFieldName == "sel" }]
                    [{ oxmultilang ident="PLEASE_CHOOSE" }]
                [{else}]
                    [{ oxmultilang ident="CHOOSE_VARIANT" }]
                [{/if}]
            </span>
        [{/if}]
    </p>
    [{if $editable !== false}]
        <input type="hidden" name="[{$sFieldName|default:"varselid"}][[{$iKey}]]" value="[{if $oActiveSelection }][{$oActiveSelection->getValue()}][{/if}]">
        <ul class="drop [{$sSelType|default:"vardrop"}] FXgradGreyLight shadow">
            [{if $oActiveSelection && !$blHideDefault}]
                <li><a rel="" href="#">
                    [{if $sFieldName == "sel" }]
                        [{ oxmultilang ident="PLEASE_CHOOSE" }]
                    [{else}]
                        [{ oxmultilang ident="CHOOSE_VARIANT" }]
                    [{/if}]
                </a></li>
            [{/if}]
            [{foreach from=$oSelections item=oSelection}]
                <li class="[{if $oSelection->isDisabled()}]js-disabled disabled[{/if}]">
                    <a data-selection-id="[{$oSelection->getValue()}]" href="[{$oSelection->getLink()}]" class="[{if $oSelection->isActive()}]selected[{/if}]">[{$oSelection->getName()}]</a>
                </li>
            [{/foreach}]
        </ul>
    [{/if}]
</div>
[{else}]
<a href="[{ $_productLink }]" class="variantMessage">
[{if $sFieldName == "sel" }]
    [{ oxmultilang ident="PLEASE_CHOOSE" }]
[{else}]
    [{ oxmultilang ident="CHOOSE_VARIANT" }]
[{/if}]
</a>
[{/if}]