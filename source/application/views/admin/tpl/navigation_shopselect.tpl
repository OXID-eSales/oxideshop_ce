[{oxscript include="js/libs/jquery.min.js"}]
[{oxscript include="js/libs/jquery-ui.min.js"}]
[{oxscript include="js/libs/chosen/chosen.jquery.min.js"}]
[{oxscript include="js/widgets/oxshopselect.js"}]
[{oxstyle include="css/libs/chosen/chosen.min.css"}]

<ul>
    <li>
        [{if $oView->isMall()}]
            <form name="search" id="search" action="[{ $oViewConf->getSelfLink() }]" method="post">
                [{ $oViewConf->getHiddenSid() }]
                <input type="hidden" name="cl" value="[{$oViewConf->getActiveClassName()}]">
                <input type="hidden" name="item" value="navigation.tpl">
                <input type="hidden" name="fnc" value="chshp">
                <select id="selectshop" class="folderselect" onChange="selectShop( this.value );">
                [{foreach from=$shoplist item=oShop}]
                    <option value="[{ $oShop->oxshops__oxid->value|default:$oViewConf->getActiveShopId() }]" [{ if $oViewConf->getActiveShopId() == $oShop->oxshops__oxid->value|default:$oViewConf->getActiveShopId() }]SELECTED[{/if}] >[{$oShop->oxshops__oxname->value|default:$actshop}]</option>
                [{/foreach}]
                </select>
            </form>
        [{/if}]
    </li>
</ul>

[{oxscript}]
[{oxstyle}]