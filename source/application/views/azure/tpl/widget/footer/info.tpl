[{assign var="aServices" value=$oView->getServicesList() }]
[{block name="footer_information"}]
    <dl id="footerInformation">
        <dt>[{oxmultilang ident="INFORMATION" }]</dt>
        <dd>
            <ul class="list services">
                [{foreach from=$aServices item=oService name=serviceList}]
                    <li><a href="[{ $oService->getLink() }]">[{ $oService->oxcontents__oxtitle->value }]</a></li>
                [{/foreach}]
                <li><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=newsletter" }]" rel="nofollow">[{ oxmultilang ident="NEWSLETTER" }]</a></li>
            </ul>
        </dd>
    </dl>
[{/block}]