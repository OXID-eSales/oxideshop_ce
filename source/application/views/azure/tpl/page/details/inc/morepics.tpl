[{if $oView->morePics()}]
<div class="otherPictures" id="morePicsContainer">
    <div class="shadowLine"></div>
    <ul class="clear">
    [{oxscript add="var aMorePic=new Array();"}]
    [{foreach from=$oView->getIcons() key=iPicNr item=oArtIcon name=sMorePics}]
        <li>
            <a id="morePics_[{$smarty.foreach.sMorePics.iteration}]" rel="useZoom: 'zoom1', smallImage: '[{$oPictureProduct->getPictureUrl($iPicNr)}]' " class="cloud-zoom-gallery" href="[{$oPictureProduct->getMasterZoomPictureUrl($iPicNr)}]">
                <span class="marker"><img src="[{$oViewConf->getImageUrl('marker.png')}]" alt=""></span>
                <span class="artIcon"><img src="[{$oPictureProduct->getIconUrl($iPicNr)}]" alt=""></span>
            </a>
        </li>
    [{/foreach}]
    </ul>
    </div>
[{/if}]
[{oxscript include="js/widgets/oxmorepictures.js" priority=10}]
[{oxscript add="$('#morePicsContainer').oxMorePictures();"}]