[{assign var="oDetailsProduct" value=$oView->getProduct()}]
[{assign var="oPictureProduct" value=$oView->getPicturesProduct()}]
[{assign var="currency" value=$oView->getActCurrency()}]
[{assign var="sPageHeadTitle" value=$oDetailsProduct->oxarticles__oxtitle->value|cat:' '|cat:$oDetailsProduct->oxarticles__oxvarselect->value}]

[{if $oView->getPriceAlarmStatus() == 1}]
[{assign var="shop_name" value=$oxcmp_shop->oxshops__oxname->value}]
[{assign var="bid_price" value=$oView->getBidPrice()}]
[{assign var="currency_sign" value=$currency->sign}]
[{assign_adv var="message_vars" value="array
    (
     '0' => '$shop_name',
     '1' => '$bid_price',
     '2' => '$currency_sign'
    )"}]
[{assign var="_statusMessage" value="PRICE_ALERT_THANK_YOU_MESSAGE"|oxmultilangassign:$message_vars}]
[{include file="message/success.tpl" statusMessage=`$_statusMessage`}]
[{elseif $oView->getPriceAlarmStatus() == 2}]
[{assign var="_statusMessage" value="MESSAGE_WRONG_VERIFICATION_CODE"|oxmultilangassign}]
[{include file="message/error.tpl" statusMessage=$_statusMessage}]
[{elseif $oView->getPriceAlarmStatus() === 0}]
[{assign var="_statusMessage1" value="MESSAGE_NOT_ABLE_TO_SEND_EMAIL"|oxmultilangassign|cat:"<br> "}]
[{assign var="_statusMessage2" value="MESSAGE_VERIFY_YOUR_EMAIL"|oxmultilangassign}]
[{include file="message/error.tpl" statusMessage=`$_statusMessage1``$_statusMessage2`}]
[{/if}]

<div id="details">
    [{ if $oView->getSearchTitle() }]
        [{ assign var="detailsLocation" value=$oView->getSearchTitle()}]
    [{else}]
        [{foreach from=$oView->getCatTreePath() item=oCatPath name="detailslocation"}]
            [{if $smarty.foreach.detailslocation.last}]
                [{assign var="detailsLocation" value=$oCatPath->oxcategories__oxtitle->value}]
            [{/if}]
        [{/foreach}]
    [{/if }]


    [{* details locator  *}]
    [{assign var="actCategory" value=$oView->getActiveCategory()}]
    <div id="overviewLink">
        <a href="[{ $actCategory->toListLink }]" class="overviewLink">[{ oxmultilang ident="BACK_TO_OVERVIEW" }]</a>
    </div>
    <h2 class="pageHead">[{$sPageHeadTitle|truncate:80}]</h2>
    <div class="detailsParams listRefine bottomRound">
        <div class="pager refineParams clear" id="detailsItemsPager">
            [{if $actCategory->prevProductLink}]<a id="linkPrevArticle" class="prev" href="[{$actCategory->prevProductLink}]">[{oxmultilang ident="PREVIOUS_PRODUCT"}]</a>[{/if}]
            <span class="page">
               [{oxmultilang ident="PRODUCT"}] [{$actCategory->iProductPos}] [{oxmultilang ident="OF"}] [{$actCategory->iCntOfProd}]
            </span>
            [{if $actCategory->nextProductLink}]<a id="linkNextArticle" href="[{$actCategory->nextProductLink}]" class="next">[{oxmultilang ident="NEXT_PRODUCT"}]</a>[{/if}]
        </div>
    </div>

    [{* RDFa offering*}]
    <div id="productinfo">
        [{include file="page/details/inc/fullproductinfo.tpl"}]
    </div>
</div>
[{ insert name="oxid_tracker" title="PRODUCT_DETAILS"|oxmultilangassign product=$oDetailsProduct cpath=$oView->getCatTreePath() }]
[{oxscript widget=$oView->getClassName()}]
