[{assign var="vendors" value=$oView->getVendorlist()}]
[{if $vendors->count()}]
    [{block name="footer_vendors"}]
    <dl id="footerVendors" class="list vendors">
        <dt class="list-header">[{oxmultilang ident="DISTRIBUTORS"}]</dt>
        [{foreach from=$vendors item=_vnd}]
            <dd><a href="[{$_vnd->getLink()}]" [{if $_vnd->expanded}]class="exp"[{/if}]>[{$_vnd->oxvendor__oxtitle->value}]</a></dd>
        [{/foreach}]
    </dl>
    [{/block}]
[{/if}]