[{foreach from=$aErrors item=oError}]
  <span class="js-oxError_postError">[{$oError->getMessage()}]</span>
[{/foreach}]