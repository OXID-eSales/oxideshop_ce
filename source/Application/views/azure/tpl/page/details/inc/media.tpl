[{assign var="oConfig" value=$oViewConf->getConfig()}]
[{oxscript add="$('a.js-external').attr('target', '_blank');"}]
[{if $oDetailsProduct->oxarticles__oxfile->value}]
  <a id="productFile" class="js-external" href="[{$oConfig->getPictureUrl('media/')}][{$oDetailsProduct->oxarticles__oxfile->value}]">[{$oDetailsProduct->oxarticles__oxfile->value}]</a>
[{/if}]

[{if $oView->getMediaFiles()}]
  <div>
    [{foreach from=$oView->getMediaFiles() item=oMediaUrl}]
      <p>[{$oMediaUrl->getHtml()}]</p>
    [{/foreach}]
  </div>
[{/if}]