[{assign var="vendors" value=$oView->getVendorlist()}]
[{if $vendors->count()}]
    [{block name="footer_vendors"}]
    <dl id="footerVendors">
        <dt>[{oxmultilang ident="DISTRIBUTORS" }]</dt>
        <dd>
            <ul class="list">
              [{foreach from=$vendors item=_vnd}]
              <li><a href="[{$_vnd->getLink()}]" [{if $_vnd->expanded}]class="exp"[{/if}]>[{$_vnd->oxvendor__oxtitle->value}]</a></li>
              [{/foreach}]
            </ul>
        </dd>
    </dl>
    [{/block}]
[{/if}]