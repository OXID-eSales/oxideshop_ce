[{assign var="iManufacturerLimit" value="20"}]
<ul class="list">
    [{foreach from=$manufacturers item=_mnf name=manufacturers}]
        [{if $smarty.foreach.manufacturers.index < $iManufacturerLimit}]    
            <li><a href="[{$_mnf->getLink()}]" [{if $_mnf->expanded}]class="exp"[{/if}]>[{$_mnf->oxmanufacturers__oxtitle->value}]</a></li>
        [{elseif $smarty.foreach.manufacturers.index == $iManufacturerLimit}]
            [{assign var="rootManufacturer" value=$oView->getRootManufacturer()}]
            <li><a href="[{$rootManufacturer->getLink()}]">[{oxmultilang ident="WIDGET_FOOTER_MANUFACTURERS_MORE"}]</a></li>
        [{/if}]
    [{/foreach}]
</ul>