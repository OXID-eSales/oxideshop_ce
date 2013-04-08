<div class="wishlistSuggest clear">
    [{if $oView->getWishList() && $oView->showSuggest() }]
        [{if count($Errors.account_whishlist)>0 }]
            <div class="inlineError">
                [{foreach from=$Errors.account_whishlist item=oEr key=key }]
                    [{ $oEr->getOxMessage()}]<br>
                [{/foreach}]
            </div>
        [{/if}]
        [{assign var="editval" value=$oView->getEnteredData() }]
        <form action="[{ $oViewConf->getSelfActionLink() }]" method="post">
            <div>
            [{ $oViewConf->getHiddenSid() }]
            <input type="hidden" name="fnc" value="sendwishlist">
            <input type="hidden" name="cl" value="account_wishlist">
            <input type="hidden" name="blshowsuggest" value="1">
            <input type="hidden" name="editval[send_subject]" value="[{ oxmultilang ident="FORM_WISHLIST_SUGGEST_GIFTREGISTRYAT" }] [{ $oxcmp_shop->oxshops__oxname->value }]">
            <input type="hidden" name="CustomError" value='account_whishlist'>
            <ul class="form clear">
                <li class="formTitle"><label>[{ oxmultilang ident="FORM_WISHLIST_SUGGEST_SENDWISHLIST" }]</label></li>
                <li>
                    <label>[{ oxmultilang ident="FORM_WISHLIST_SUGGEST_RECIPIENTSNAME" }]</label>
                    <input type="Text" name="editval[rec_name]" size="37" maxlength="70" value="[{ $editval->rec_name }]">
                </li>
                <li>
                    <label>[{ oxmultilang ident="FORM_WISHLIST_SUGGEST_RECIPIENTSEMAIL" }]</label>
                    <input type="Text" name="editval[rec_email]" size="37" maxlength="70" value="[{ $editval->rec_email }]">
                </li>
                <li>
                    <label>[{ oxmultilang ident="FORM_WISHLIST_SUGGEST_YUORMESSAGE" }]</label>
                    <textarea rows="6" class="areabox" cols="68" name="editval[send_message]">[{if $editval->send_message }][{ $editval->send_message }][{else }][{ oxmultilang ident="FORM_WISHLIST_SUGGEST_BUYFORME1" }] [{ $oxcmp_shop->oxshops__oxname->value }] [{ oxmultilang ident="FORM_WISHLIST_SUGGEST_BUYFORME2" }][{/if }]</textarea>
                </li>
                <li class="formSubmit"><button class="submitButton" type="submit">[{ oxmultilang ident="FORM_WISHLIST_SUGGEST_SEND2" }]</button></li>
            </ul>
            </div>
        </form>
    [{/if }]
</div>