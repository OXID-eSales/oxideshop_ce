[{if $selectedStateIdPrim}]
  [{assign var=selectedStateId value=$selectedStateIdPrim}]
[{/if}]

[{assign var=divId value=oxStateDiv_$stateSelectName}]
[{assign var=stateSelectId value=oxStateSelect_$stateSelectName}]

[{if $currCountry }]
  [{assign var=showDiv value='true'}]
[{else}]
  [{assign var=showDiv value='false'}]
[{/if}]
[{oxscript include="js/widgets/oxcountrystateselect.js" priority=10 }]
[{oxscript add="$( '#`$countrySelectId`' ).oxCountryStateSelect({selectedStateId:'`$selectedStateId`'});"}]

<script type="text/javascript"><!--
  var allStates = new Array();
  var allStateIds = new Array();
  var allCountryIds = new Object();
  var cCount = 0;
  [{foreach from=$oViewConf->getCountryList() item=country key=country_id }]

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
<span id="[{$divId}]" class=stateSelector>
  <select name="[{$stateSelectName}]" id="[{$stateSelectId}]" data-fieldsize="normal">
      <option value="">[{oxmultilang ident="PLEASE_SELECT_STATE"}]</option>
  </select>
</span>