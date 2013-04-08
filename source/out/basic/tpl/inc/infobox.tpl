<strong class="h2" id="test_LeftSideInfoHeader">[{ oxmultilang ident="INC_LEFTITEM_INFORMATION" }]</strong>
[{strip}]
<ul class="info">
    [{oxifcontent ident="oxsecurityinfo" object="oCont"}]
    <li><a id="test_infoProtection" href="[{ $oCont->getLink() }]" rel="nofollow">[{ $oCont->oxcontents__oxtitle->value }]</a></li>
    [{/oxifcontent}]
    [{oxifcontent ident="oxdeliveryinfo" object="oCont"}]
    <li><a id="test_infoShipping" href="[{ $oCont->getLink() }]" rel="nofollow">[{ $oCont->oxcontents__oxtitle->value }]</a></li>
    [{/oxifcontent}]
    [{oxifcontent ident="oxrightofwithdrawal" object="oCont"}]
    <li><a id="test_infoRights" href="[{ $oCont->getLink() }]" rel="nofollow">[{ $oCont->oxcontents__oxtitle->value }]</a></li>
    [{/oxifcontent}]
    [{oxifcontent ident="oxorderinfo" object="oCont"}]
    <li><a id="test_infoHowToOrder" href="[{ $oCont->getLink() }]" rel="nofollow">[{ $oCont->oxcontents__oxtitle->value }]</a></li>
    [{/oxifcontent}]
    [{oxifcontent ident="oxcredits" object="oCont"}]
    <li><a id="test_infoCredits" href="[{ $oCont->getLink() }]" rel="nofollow">[{ $oCont->oxcontents__oxtitle->value }]</a></li>
    [{/oxifcontent}]
    <li><a id="test_infoNewsletter" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=newsletter" }]" rel="nofollow">[{ oxmultilang ident="INC_INFOBOX_NEWSLETTER" }]</a></li>
</ul>
[{/strip}]
