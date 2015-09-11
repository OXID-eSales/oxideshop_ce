[{capture append="oxidBlock_content"}]

    [{if $oView->getProduct()}]
        [{assign var="product" value=$oView->getProduct()}]
        [{assign var="_productLink" value=$product->getLink()}]

        <div class="lineView clear">
            [{block name="widget_product_listitem_line_picturebox"}]
            <div class="pictureBox">
                <a class="sliderHover" href="[{$_productLink}]" title="[{$product->oxarticles__oxtitle->value}]"></a>
                <a href="[{$_productLink}]" class="viewAllHover glowShadow corners" title="[{$product->oxarticles__oxtitle->value}]"><span>[{oxmultilang ident="PRODUCT_DETAILS"}]</span></a>
                <img src="[{$product->getThumbnailUrl()}]" alt="[{$product->oxarticles__oxtitle->value}]">
            </div>
            [{/block}]

            <div class="infoBox">
                [{block name="widget_product_listitem_line_selections"}]

                    <div class="info">
                        <a id="[{$testid}]" href="[{$_productLink}]" class="title" title="[{$product->oxarticles__oxtitle->value}]">
                            <span>[{$product->oxarticles__oxtitle->value}]</span>
                        </a>
                    </div>

                    <div class="description">
                        [{oxhasrights ident="SHOWSHORTDESCRIPTION"}]
                            [{$product->oxarticles__oxshortdesc->value|truncate:160:"..."}]
                        [{/oxhasrights}]
                    </div>
                [{/block}]
            </div>
        </div>


        <br>

    [{/if}]

    [{if $oView->isReviewActive()}]
    <div class="widgetBox reviews">
        <h4>[{oxmultilang ident="WRITE_PRODUCT_REVIEW"}]</h4>
        [{assign var="product" value=$oView->getProduct()}]
        [{if $oxcmp_user}]
            [{assign var="force_sid" value=$oView->getSidForWidget()}]
        [{/if}]
        [{oxid_include_widget cl="oxwReview" nocookie=1 force_sid=$force_sid _parent=$oView->getClassName() type=oxarticle anid=$product->oxarticles__oxnid->value aid=$product->oxarticles__oxid->value canrate=$oView->canRate() reviewuserhash=$oView->getReviewUserHash() skipESIforUser=1}]
    </div>
    [{/if}]
[{/capture}]
[{include file="layout/page.tpl" sidebar="Left"}]

