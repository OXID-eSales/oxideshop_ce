[{oxscript include="js/widgets/oxmodalpopup.js" priority=10}]
[{oxscript add="$('.wrappingTrigger').oxModalPopup({target: '.wrapping'});"}]

<div class="wrapping popupBox corners FXgradGreyLight glowShadow">
    <img src="[{$oViewConf->getImageUrl('x.png')}]" alt="" class="closePop">
    [{assign var="currency" value=$oView->getActCurrency()}]
    [{block name="checkout_wrapping_header"}]
        <div class="wrappingIntro clear">
            <h3>[{oxmultilang ident="GIFT_OPTION"}]</h3>
            <img src="[{$oViewConf->getImageUrl('gift-wrapping.jpg')}]" alt="[{oxmultilang ident="ADD_WRAPPING"}]">
            <div class="introtext">
                [{oxmultilang ident="WRAPPING_DESCRIPTION"}]
            </div>
        </div>
    [{/block}]

    [{block name="checkout_wrapping_contents"}]
        <h3 class="blockHead">[{oxmultilang ident="ADD_WRAPPING"}]</h3>
        [{if !$oxcmp_basket->getProductsCount()}]
            <div>[{oxmultilang ident="BASKET_EMPTY"}]</div>
        [{else}]
            <form name="basket" action="[{$oViewConf->getSelfActionLink()}]" method="post">
                [{$oViewConf->getHiddenSid()}]
                <input type="hidden" name="cl" value="basket">
                <input type="hidden" name="fnc" value="changewrapping">
                [{assign var="oWrapList" value=$oView->getWrappingList()}]
                [{if $oWrapList->count()}]
                    [{* basket items *}]
                    [{assign var="icounter" value="0"}]
                    <table class="wrappingData">
                        <colgroup>
                            <col class="thumbCol">
                            <col class="articleCol">
                            <col class="priceCol">
                        </colgroup>
                        [{assign var="basketitemlist" value=$oView->getBasketArticles()}]
                        [{foreach key=basketindex from=$oxcmp_basket->getContents() item=basketitem name=wrappArt}]
                            [{block name="checkout_wrapping_item"}]
                                [{assign var="basketproduct" value=$basketitemlist.$basketindex}]
                                <tr>
                                    <td>
                                        <a href="[{$basketitem->getLink()}]">
                                            <img src="[{$basketproduct->getThumbnailUrl()}]" alt="[{$basketitem->getTitle()|strip_tags}]">
                                        </a>
                                    </td>
                                    <td>
                                        <a rel="nofollow" href="[{$basketitem->getLink()}]">[{$basketitem->getTitle()}]</a>
                                    </td>
                                    <td>
                                        <ul id="wrapp_[{$smarty.foreach.wrappArt.iteration}]">
                                            <li>
                                                <input class="radiobox" type="radio" name="wrapping[[{$basketindex}]]" id="wrapping_[{$basketindex}]" value="0" [{if !$basketitem->getWrappingId()}]CHECKED[{/if}]>
                                                <label for="wrapping_[{$basketindex}]">[{oxmultilang ident="NONE"}]</label><strong>[{oxprice price=0 currency=$currency}]</strong>
                                            </li>
                                            [{assign var="ictr" value="1"}]
                                            [{foreach from=$oView->getWrappingList() item=wrapping name=Wraps}]
                                                <li>
                                                    <input class="radiobox" type="radio" name="wrapping[[{$basketindex}]]" id="wrapping_[{$wrapping->oxwrapping__oxid->value}]" value="[{$wrapping->oxwrapping__oxid->value}]" [{if $basketitem->getWrappingId() == $wrapping->oxwrapping__oxid->value}]CHECKED[{/if}]>
                                                    [{if $wrapping->oxwrapping__oxpic->value}]
                                                    <span><img src="[{$wrapping->getPictureUrl()}]" alt="[{$wrapping->oxwrapping__oxname->value}]"></span>
                                                    [{/if}]
                                                    <label for="wrapping_[{$wrapping->oxwrapping__oxid->value}]">[{$wrapping->oxwrapping__oxname->value}]</label>
                                                    <strong>[{oxprice price=$wrapping->getPrice() currency=$currency}]</strong>
                                                </li>
                                                [{assign var="ictr" value="`$ictr+1`"}]
                                            [{/foreach}]
                                            [{oxscript add="$('#wrapp_`$smarty.foreach.wrappArt.iteration` img' ).click(function(){ $(this).parent().parent().find('input').click();});"}]
                                        </ul>

                                    </td>
                                </tr>
                                [{assign var="icounter" value="`$icounter+1`"}]
                            [{/block}]
                        [{/foreach}]
                    </table>
                [{/if}]

                [{assign var="oCardList" value=$oView->getCardList()}]
                [{if $oCardList->count()}]
                    [{block name="checkout_wrapping_cards"}]
                        <h3 class="blockHead">[{oxmultilang ident="GREETING_CARD"}]</h3>
                        <ul class="wrappingCard clear" id="wrappCard">
                            <li>
                                <p class="clear">
                                    <input type="radio" class="radiobox" name="chosencard" id="chosencard" value="0" [{if !$oxcmp_basket->getCardId()}]CHECKED[{/if}]>
                                    <label for="chosencard">[{oxmultilang ident="NO_GREETING_CARD"}]</label>
                                </p>
                            </li>
                        [{assign var="icounter" value="0"}]
                        [{counter start=0 print=false}]
                        [{assign var="icounter" value="0"}]
                        [{foreach from=$oCardList item=card name=GreetCards}]
                            <li>
                                <p class="clear">
                                    <input class="radiobox" type="radio" name="chosencard" id="chosen_[{$card->oxwrapping__oxid->value}]" value="[{$card->oxwrapping__oxid->value}]" [{if $oxcmp_basket->getCardId() == $card->oxwrapping__oxid->value}]CHECKED[{/if}]>
                                    <label for="chosen_[{$card->oxwrapping__oxid->value}]">[{$card->oxwrapping__oxname->value}] <strong>[{oxprice price=$card->getPrice() currency=$currency}]</strong></label>
                                </p>
                                [{if $card->oxwrapping__oxpic->value}]
                                <img src="[{$card->getPictureUrl()}]" alt="[{$card->oxwrapping__oxname->value}]">
                                [{/if}]
                            </li>
                        [{assign var="icounter" value="`$icounter+1`"}]
                        [{/foreach}]
                        [{oxscript add="$('#wrappCard img').click(function(){ $(this).parent().find('input').click(); });"}]
                        </ul>

                    [{/block}]
                    [{block name="checkout_wrapping_comment"}]
                        <div class="wrappingComment">
                            <label>[{oxmultilang ident="GREETING_MESSAGE"}]</label>
                            <textarea cols="102" rows="5" name="giftmessage" class="areabox">[{$oxcmp_basket->getCardMessage()}]</textarea>
                        </div>
                    [{/block}]
                [{/if}]
                [{block name="checkout_wrapping_submit"}]
                    <div class="submitForm clear">
                        <button type="submit" class="submitButton largeButton">[{oxmultilang ident="APPLAY"}]</button>
                        <button class="textButton largeButton closePop">[{oxmultilang ident="CANCEL"}]</button>
                    </div>
                [{/block}]
            </form>
        [{/if}]
    [{/block}]
</div>