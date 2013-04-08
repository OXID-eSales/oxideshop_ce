[{foreach from=$aErrors item=oError }]
  <span class="js-oxError_postError">[{oxmultilang ident=$oError->getMessage()}]</span>
[{/foreach }]