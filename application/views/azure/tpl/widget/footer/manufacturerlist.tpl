[{assign var="iManufacturerLimit" value="20"}]
[{assign var="manufacturers" value=$oView->getManufacturerlist()}]
[{if $manufacturers|count}]
    [{block name="footer_manufacturers"}]
    <dl id="footerManufacturers">
        <dt>[{oxmultilang ident="MANUFACTURERS" }]</dt>
        <dd>
        <ul class="list">
            [{assign var="rootManufacturer" value=$oView->getRootManufacturer()}]
            <li><a href="[{$rootManufacturer->getLink()}]">[{oxmultilang ident="ALL_BRANDS"}]</a></li>
            [{foreach from=$manufacturers item=_mnf name=manufacturers}]
                [{if $smarty.foreach.manufacturers.index < $iManufacturerLimit}]
                    <li><a href="[{$_mnf->getLink()}]" [{if $_mnf->expanded}]class="exp"[{/if}]>[{$_mnf->oxmanufacturers__oxtitle->value}]</a></li>
                [{elseif $smarty.foreach.manufacturers.index == $iManufacturerLimit}]
                    <li><a href="[{$rootManufacturer->getLink()}]">[{oxmultilang ident="MORE"}]</a></li>
                [{/if}]
            [{/foreach}]
        </ul>
        </dd>
    </dl>
    [{/block}]
[{/if}]
