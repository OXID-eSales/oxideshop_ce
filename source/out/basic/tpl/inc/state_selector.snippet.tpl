[{if $selectedStateIdPrim}]
  [{assign var=selectedStateId value=$selectedStateIdPrim}]
[{/if}]

[{assign var=divId value=oxStateDiv_$stateSelectName}]
[{assign var=stateSelectId value=oxStateSelect_$stateSelectName}]
[{assign var=statePromptString value='STATE_PROMPT'|oxmultilangassign}]


[{if $currCountry }]
  [{assign var=showDiv value='true'}]
[{else}]
  [{assign var=showDiv value='false'}]
[{/if}]

[{oxscript add="oxid.stateSelector.fillStates('`$countrySelectId`', '`$stateSelectId`', '`$divId`', allStates, allStateIds, allCountryIds, '`$statePromptString`', '`$selectedStateId`');"}]

<script language=JavaScript><!--
  var allStates = new Array();
  var allStateIds = new Array();
  var allCountryIds = new Object();
  var cCount = 0;
  [{foreach from=$oView->getCountryList() item=country key=country_id }]

    var states = new Array();
    var ids = new Array();
    var i = 0;

    [{assign var=countryStates value=$country->getStates()}]
    [{foreach from=$countryStates item=state key=state_id}]
        states[i] = '[{$state->oxstates__oxtitle->value}]';
        ids[i] = '[{$state->oxstates__oxid->value}]';
        i++;
    [{/foreach}]
    allStates[++cCount] = states;
    allStateIds[cCount]  = ids;
    allCountryIds['[{$country->getId()}]']  = cCount;
  [{/foreach}]

--></script>

<span id="[{$divId}]" style="display:none;" class=stateSelector>

  <select name="[{$stateSelectName}]" id="[{$stateSelectId}]">
  </select>
</span>

<noscript>
   <select name="[{$stateSelectName}]">
   <option value="">[{ oxmultilang ident="STATE_PROMPT"  }]</option>
   [{foreach from=$oViewConf->getCountryList() item=country key=country_id }]
        [{assign var=countryStates value=$country->getStates()}]
        [{if $countryStates->count()}]
        <option value="">-- [{ $country->oxcountry__oxtitle->value  }] --</option>
        [{foreach from=$countryStates item=state}]
                <option value="[{$state->oxstates__oxid->value}]" [{if $state->oxstates__oxid->value == $selectedStateId }]SELECTED[{/if}]> [{ $state->oxstates__oxtitle->value  }]</option>
        [{/foreach}]
        [{/if}]
  [{/foreach}]
  </select>
</noscript>