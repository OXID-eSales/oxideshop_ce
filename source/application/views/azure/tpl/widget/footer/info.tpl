[{block name="footer_information"}]
    <dl id="footerInformation">
        <dt>[{oxmultilang ident="FOOTER_INFORMATION" }]</dt>
        <dd>
            <ul class="list services">
                [{oxifcontent ident="oximpressum" object="_cont"}]
                    <li><a href="[{ $_cont->getLink() }]">[{ $_cont->oxcontents__oxtitle->value }]</a></li>
                [{/oxifcontent}]
                [{oxifcontent ident="oxagb" object="_cont"}]
                    <li><a href="[{ $_cont->getLink() }]" rel="nofollow">[{ $_cont->oxcontents__oxtitle->value }]</a></li>
                [{/oxifcontent}]
                [{oxifcontent ident="oxsecurityinfo" object="oCont"}]
                    <li><a href="[{ $oCont->getLink() }]" rel="nofollow">[{ $oCont->oxcontents__oxtitle->value }]</a></li>
                [{/oxifcontent}]
                [{oxifcontent ident="oxdeliveryinfo" object="oCont"}]
                    <li><a href="[{ $oCont->getLink() }]" rel="nofollow">[{ $oCont->oxcontents__oxtitle->value }]</a></li>
                [{/oxifcontent}]
                [{oxifcontent ident="oxrightofwithdrawal" object="oCont"}]
                    <li><a href="[{ $oCont->getLink() }]" rel="nofollow">[{ $oCont->oxcontents__oxtitle->value }]</a></li>
                [{/oxifcontent}]
                [{oxifcontent ident="oxorderinfo" object="oCont"}]
                    <li><a href="[{ $oCont->getLink() }]" rel="nofollow">[{ $oCont->oxcontents__oxtitle->value }]</a></li>
                [{/oxifcontent}]
                [{oxifcontent ident="oxcredits" object="oCont"}]
                    <li><a href="[{ $oCont->getLink() }]" rel="nofollow">[{ $oCont->oxcontents__oxtitle->value }]</a></li>
                [{/oxifcontent}]
                <li><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=newsletter" }]" rel="nofollow">[{ oxmultilang ident="WIDGET_SERVICES_NEWSLETTER" }]</a></li>
            </ul>
        </dd>
    </dl>
[{/block}]