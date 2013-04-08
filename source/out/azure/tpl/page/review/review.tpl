[{assign var="template_title" value="REVIEW_YOURREVIEW"|oxmultilangassign }]

[{capture append="oxidBlock_content"}]

    [{if $oView->getProduct()}]
        [{assign var="product" value=$oView->getProduct()}]
        [{assign var="_productLink" value=$product->getLink()}]

        <div class="lineView clear">
            [{block name="widget_product_listitem_line_picturebox"}]
            <div class="pictureBox">
                <a class="sliderHover" href="[{ $_productLink }]" title="[{ $product->oxarticles__oxtitle->value}]"></a>
                <a href="[{$_productLink}]" class="viewAllHover glowShadow corners" title="[{ $product->oxarticles__oxtitle->value}]"><span>[{oxmultilang ident="WIDGET_PRODUCT_PRODUCT_DETAILS"}]</span></a>
                <img src="[{$product->getThumbnailUrl()}]" alt="[{ $product->oxarticles__oxtitle->value}]">
            </div>
            [{/block}]

            <div class="infoBox">
                [{block name="widget_product_listitem_line_selections"}]

                    <div class="info">
                        <a id="[{$testid}]" href="[{$_productLink}]" class="title" title="[{ $product->oxarticles__oxtitle->value}]">
                            <span>[{ $product->oxarticles__oxtitle->value }]</span>
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

    [{if $oView->isReviewActive() }]
    <div class="widgetBox reviews">
        <h4>[{oxmultilang ident="DETAILS_PRODUCTREVIEW"}]</h4>
        [{include file="widget/reviews/reviews.tpl" sReviewUserHash=$oView->getReviewUserHash() oDetailsProduct=$oView->getProduct() oReviewUser=$oView->getReviewUser() }]
    </div>
    [{/if}]
[{/capture}]
[{include file="layout/page.tpl" sidebar="Left"}]

