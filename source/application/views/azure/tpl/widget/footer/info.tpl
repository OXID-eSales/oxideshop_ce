[{assign var="aServices" value=$oView->getServicesList()}]
[{block name="footer_information"}]
    <dl id="footerInformation">
        <dt>[{oxmultilang ident="INFORMATION" }]</dt>
        <dd>
            <ul class="list services">
                <li><a href="[{ $aServices.oximpressum->getLink() }]">[{ $aServices.oximpressum->oxcontents__oxtitle->value }]</a></li>
                <li><a href="[{ $aServices.oxagb->getLink() }]">[{ $aServices.oxagb->oxcontents__oxtitle->value }]</a></li>
                <li><a href="[{ $aServices.oxsecurityinfo->getLink() }]">[{ $aServices.oxsecurityinfo->oxcontents__oxtitle->value }]</a></li>
                <li><a href="[{ $aServices.oxdeliveryinfo->getLink() }]">[{ $aServices.oxdeliveryinfo->oxcontents__oxtitle->value }]</a></li>
                <li><a href="[{ $aServices.oxrightofwithdrawal->getLink() }]">[{ $aServices.oxrightofwithdrawal->oxcontents__oxtitle->value }]</a></li>
                <li><a href="[{ $aServices.oxorderinfo->getLink() }]">[{ $aServices.oxorderinfo->oxcontents__oxtitle->value }]</a></li>
                <li><a href="[{ $aServices.oxcredits->getLink() }]">[{ $aServices.oxcredits->oxcontents__oxtitle->value }]</a></li>
                <li><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=newsletter" }]" rel="nofollow">[{ oxmultilang ident="NEWSLETTER" }]</a></li>
            </ul>
        </dd>
    </dl>
[{/block}]