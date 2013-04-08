<!-- Trusted Shops Siegel -->
[{if $oView->getTrustedShopId() }]
  [{assign var="tsId" value=$oView->getTrustedShopId() }]
[{/if}]
[{if $oView->getTSExcellenceId() }]
  [{assign var="tsId" value=$oView->getTSExcellenceId() }]
[{/if}]
[{if $tsId }]
<div id="tsBox" style="padding:0 10px;">
    <div style="background-color:#FFFFFF;width:151px;font-family: Verdana, Arial, Helvetica, sans-serif;background-image: url([{$oViewConf->getImageUrl()}]bg_yellow.jpg);background-repeat: repeat;background-position: left top;vertical-align:middle;margin-top:0px;border:0px solid #C0C0C0;padding:2px;" id="tsInnerBox">
        <div style="text-align:center;width:151px;float:left; border:0px solid; padding:2px;" id="tsSeal">
            <a id="tsCertificate" href="https://www.trustedshops.com/shop/certificate.php?shop_id=[{$tsId}]">
                <img style="border:0px none;" src="[{$oViewConf->getImageUrl()}]trustedshops_m.gif" title="[{ oxmultilang ident="INC_TRUSTEDSHOPS_ITEM_IMGTITLE" }]">
            </a>
            [{oxscript add="oxid.blank('tsCertificate');"}]
        </div>
        <div style="text-align:center;width:151px;line-height:125%;float:left;border:0px solid; padding:2px;" id="tsText">
            <a id="tsProfile" style="font-weight:normal;text-decoration:none;color:#000000;" title="[{ oxmultilang ident="INC_TRUSTEDSHOPS_ITEM_ALTTEXT" }]" href="[{ oxmultilang ident="INC_TRUSTEDSHOPS_ITEM_PROFILELINK" }][{$tsId}].html">
                [{$oxcmp_shop->oxshops__oxname->value}] [{ oxmultilang ident="INC_TRUSTEDSHOPS_ITEM_SEALOFAPPROVAL" }]
            </a>
            [{oxscript add="oxid.blank('tsProfile');"}]
        </div>
        <div style="clear:both;"></div>
    </div>
</div>
[{else}]
<div id="siegel">
    <a id="tsMembership" href="[{ oxmultilang ident="INC_TRUSTEDSHOPS_ITEM_LINK" }]">
        <img style="border:0px none;" src="[{$oViewConf->getImageUrl()}]trustedshops_[{$oViewConf->getActLanguageAbbr()}].gif" alt="[{ oxmultilang ident="INC_TRUSTEDSHOPS_ITEM_ALTTEXT" }]">
    </a>
    [{oxscript add="oxid.blank('tsMembership');"}]
</div>
[{/if}]
<!-- / Trusted Shops Siegel -->