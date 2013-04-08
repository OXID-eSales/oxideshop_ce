[{assign var="template_title" value="MOREDETAILS_POPUP_TITLE"|oxmultilangassign}]
[{include file="_header_plain.tpl" title=$template_title location=$template_title}]


[{include file="inc/popup_zoom.tpl" aZoomPics=$oView->getArtZoomPics() iZoomPic=$oView->getActPictureId() popup=false product=$oView->getProduct()}]


[{insert name="oxid_tracker" title=$template_title }]
[{include file="_footer_plain.tpl"}]
