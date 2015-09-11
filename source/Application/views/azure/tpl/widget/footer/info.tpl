[{assign var="aServices" value=$oView->getServicesList()}]
[{assign var="aServiceItems" value=$oView->getServicesKeys()}]
[{block name="footer_information"}]
    <dl id="footerInformation" class="list information">
        <dt class="list-header">[{oxmultilang ident="INFORMATION"}]</dt>
        [{foreach from=$aServiceItems item=sItem}]
            [{if isset($aServices.$sItem)}]
                <dd><a href="[{$aServices.$sItem->getLink()}]">[{$aServices.$sItem->oxcontents__oxtitle->value}]</a></dd>
            [{/if}]
        [{/foreach}]
        <dd><a href="[{oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=newsletter"}]" rel="nofollow">[{oxmultilang ident="NEWSLETTER"}]</a></dd>
    </dl>
[{/block}]