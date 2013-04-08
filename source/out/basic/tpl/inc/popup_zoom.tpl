<div id="zoom" [{if $popup}]class="popup"[{/if}]>
    <ul class="tabs">
        [{assign var="aZoomPics" value=$oView->getZoomPics() }]
        [{assign var="iZoomPic" value=$oView->getActZoomPic() }]
        [{if $aZoomPics|@count > 1}]
        [{foreach from=$aZoomPics item=_zoomPic}]
        <li><a href="[{ $oViewConf->getSelfLink() }]cl=moredetails&amp;actpicid=[{$_zoomPic.id}]&amp;anid=[{ $product->oxarticles__oxnid->value }]" onclick="oxid.image('zoomImg','[{$_zoomPic.file}]');return false;">[{$_zoomPic.id}]</a></li>
        [{/foreach}]
        [{/if}]
        <li class="close"><a href="[{ $product->getLink() }]" class="close" [{if $popup}]onclick="oxid.popup.hide('zoom');return false;"[{/if}]>X</a></li>
    </ul>
    <img src="[{if !$popup}][{$aZoomPics[$iZoomPic].file}][{else}][{$oViewConf->getImageUrl()}]leer.gif[{/if}]" alt="[{ $product->oxarticles__oxtitle->value|strip_tags }] [{ $product->oxarticles__oxvarselect->value|default:'' }]" id="zoomImg"  [{if $popup}]onclick="oxid.popup.hide('zoom');"[{/if}]>
    [{if $popup}]
        [{oxscript add="oxid.popup.addResizer('zoomImg','zoom',4,32);"}]
        [{oxscript add="oxid.image('zoomImg','`$aZoomPics[$iZoomPic].file`');"}]
    [{/if}]
</div>