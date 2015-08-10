[{assign var="iManufacturerLimit" value="20"}]
[{assign var="manufacturers" value=$oView->getManufacturerlist()}]
[{if $manufacturers|count}]
    [{block name="footer_manufacturers"}]
    <dl id="footerManufacturers" class="list manufacturers">
        <dt class="list-header">[{oxmultilang ident="MANUFACTURERS" }]</dt>
        [{assign var="rootManufacturer" value=$oView->getRootManufacturer()}]
        <dd><a href="[{$rootManufacturer->getLink()}]">[{oxmultilang ident="ALL_BRANDS"}]</a></dd>
        [{foreach from=$manufacturers item=_mnf name=manufacturers}]
            [{if $smarty.foreach.manufacturers.index < $iManufacturerLimit}]
                <dd><a href="[{$_mnf->getLink()}]" [{if $_mnf->expanded}]class="exp"[{/if}]>[{$_mnf->oxmanufacturers__oxtitle->value}]</a></dd>
            [{elseif $smarty.foreach.manufacturers.index == $iManufacturerLimit}]
                <dd><a href="[{$rootManufacturer->getLink()}]">[{oxmultilang ident="MORE"}]</a></dd>
            [{/if}]
        [{/foreach}]
    </dl>
    [{/block}]
[{/if}]
